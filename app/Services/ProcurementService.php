<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Notification;
use App\Models\ProcurementRequest;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProcurementService
{
    /**
     * Buat Request Pengadaan Baru (Oleh Barista)
     */
    public function createRequest(array $data, User $user): ProcurementRequest
    {
        return DB::transaction(function () use ($data, $user) {
            $ingredient = Ingredient::with(['unit', 'category'])->findOrFail($data['ingredient_id']);

            // Buat kode unik request: REQ-YYYYMMDD-XXXX
            $datePrefix = Carbon::now('Asia/Jakarta')->format('Ymd');
            $uniqueCode = 'REQ-' . $datePrefix . '-' . strtoupper(Str::random(5));
            while (ProcurementRequest::where('request_code', $uniqueCode)->exists()) {
                $uniqueCode = 'REQ-' . $datePrefix . '-' . strtoupper(Str::random(5));
            }

            $request = ProcurementRequest::create([
                'request_code' => $uniqueCode,
                'ingredient_id' => $ingredient->id,
                'user_id' => $user->id,
                'quantity_requested' => (float) $data['quantity_requested'],
                'stock_at_request' => (float) $ingredient->current_stock,
                'min_stock_at_request' => (float) $ingredient->min_stock,
                'required_date' => $data['required_date'],
                'priority' => $data['priority'] ?? 'normal',
                'reason' => $data['reason'] ?? null,
                'status' => 'menunggu',
            ]);

            // Kirim notifikasi ke Owner
            $qtyFormatted = number_format($request->quantity_requested, 0, ',', '.');
            $unitSymbol = $ingredient->unit?->symbol ?? 'unit';
            $priorityLabel = ucfirst($request->priority);

            Notification::create([
                'type' => 'procurement_requested',
                'title' => "Request Pengadaan Baru: {$ingredient->name}",
                'message' => "{$user->name} mengajukan request pengadaan {$qtyFormatted} {$unitSymbol} untuk bahan '{$ingredient->name}'. Prioritas: {$priorityLabel}.",
                'ingredient_id' => $ingredient->id,
                'is_read' => false,
            ]);

            return $request;
        });
    }

    /**
     * Persetujuan Request oleh Owner
     */
    public function approveRequest(ProcurementRequest $request, User $owner, array $approvalData): ProcurementRequest
    {
        return DB::transaction(function () use ($request, $owner, $approvalData) {
            $request->status = 'diproses';
            $request->approved_by = $owner->id;
            $request->reviewed_at = Carbon::now('Asia/Jakarta');
            $request->supplier_name = $approvalData['supplier_name'] ?? null;
            $request->estimated_arrival = $approvalData['estimated_arrival'] ?? null;
            $request->order_notes = $approvalData['order_notes'] ?? null;
            $request->rejection_reason = null;
            $request->save();

            $ingredient = $request->ingredient;
            $supplier = $request->supplier_name ? " via {$request->supplier_name}" : '';

            // Notifikasi persetujuan
            Notification::create([
                'type' => 'procurement_approved',
                'title' => "Request Disetujui: {$ingredient->name}",
                'message' => "Request pengadaan '{$ingredient->name}' telah disetujui Owner dan sedang diproses ke supplier{$supplier}.",
                'ingredient_id' => $ingredient->id,
                'is_read' => false,
            ]);

            return $request;
        });
    }

    /**
     * Penolakan Request oleh Owner
     */
    public function rejectRequest(ProcurementRequest $request, User $owner, string $rejectionReason): ProcurementRequest
    {
        return DB::transaction(function () use ($request, $owner, $rejectionReason) {
            $request->status = 'ditolak';
            $request->approved_by = $owner->id;
            $request->reviewed_at = Carbon::now('Asia/Jakarta');
            $request->rejection_reason = $rejectionReason;
            $request->save();

            $ingredient = $request->ingredient;

            // Notifikasi penolakan
            Notification::create([
                'type' => 'procurement_rejected',
                'title' => "Request Ditolak: {$ingredient->name}",
                'message' => "Request pengadaan '{$ingredient->name}' ditolak oleh Owner. Alasan: {$rejectionReason}",
                'ingredient_id' => $ingredient->id,
                'is_read' => false,
            ]);

            return $request;
        });
    }

    /**
     * Penerimaan Barang & Pembaruan Stok Masuk Otomatis
     */
    public function receiveGoods(ProcurementRequest $request, User $receiver, array $receiveData): ProcurementRequest
    {
        return DB::transaction(function () use ($request, $receiver, $receiveData) {
            $ingredient = Ingredient::lockForUpdate()->findOrFail($request->ingredient_id);

            $qtyReceived = (float) ($receiveData['quantity_received'] ?? $request->quantity_requested);
            $stockBefore = (float) $ingredient->current_stock;
            $stockAfter = $stockBefore + $qtyReceived;

            // Perbarui stok bahan baku
            $ingredient->current_stock = $stockAfter;
            $ingredient->save();

            // Catat mutasi stok
            $movCode = 'MOV-' . Carbon::now('Asia/Jakarta')->format('YmdHis') . '-' . strtoupper(Str::random(4));
            $supplier = $receiveData['supplier_name'] ?? $request->supplier_name ?? 'Supplier';
            $invoice = $receiveData['invoice_number'] ?? '-';

            $stockMovement = StockMovement::create([
                'movement_code' => $movCode,
                'ingredient_id' => $ingredient->id,
                'type' => 'restock',
                'quantity_in' => $qtyReceived,
                'quantity_out' => 0,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => 'procurement_request',
                'reference_id' => $request->id,
                'reference_code' => $request->request_code,
                'user_id' => $receiver->id,
                'movement_date' => Carbon::now('Asia/Jakarta'),
                'notes' => "Penerimaan pengadaan ({$request->request_code}) dari {$supplier}. No Nota/Inv: {$invoice}",
            ]);

            // Perbarui status pengadaan menjadi selesai
            $request->status = 'selesai';
            $request->quantity_received = $qtyReceived;
            $request->received_by = $receiver->id;
            $request->received_at = Carbon::now('Asia/Jakarta');
            $request->expiry_date = $receiveData['expiry_date'] ?? null;
            $request->invoice_number = $receiveData['invoice_number'] ?? null;
            $request->receipt_notes = $receiveData['receipt_notes'] ?? null;
            if (!empty($receiveData['supplier_name'])) {
                $request->supplier_name = $receiveData['supplier_name'];
            }
            $request->stock_movement_id = $stockMovement->id;
            $request->save();

            // Notifikasi barang telah diterima dan stok bertambah
            $unitSymbol = $ingredient->unit?->symbol ?? 'unit';
            $qtyFormatted = number_format($qtyReceived, 0, ',', '.');
            $stockAfterFormatted = number_format($stockAfter, 0, ',', '.');

            Notification::create([
                'type' => 'stock_restocked',
                'title' => "Barang Diterima & Stok Masuk: {$ingredient->name}",
                'message' => "Sebanyak {$qtyFormatted} {$unitSymbol} {$ingredient->name} telah diterima dan diverifikasi oleh {$receiver->name}. Stok bertambah menjadi {$stockAfterFormatted} {$unitSymbol}.",
                'ingredient_id' => $ingredient->id,
                'is_read' => false,
            ]);

            return $request;
        });
    }
}

