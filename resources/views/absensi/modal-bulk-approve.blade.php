    <!-- Modal Bulk Approve -->
    <div class="modal fade" id="bulkApproveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning bg-opacity-10 border-bottom-0">
                    <h5 class="modal-title fw-bold text-warning-emphasis">
                        <i class="ti ti-shield-check me-2"></i>Verifikasi Massal Absensi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Anda akan mengubah status untuk <strong id="modal_selected_count" class="text-dark">0</strong> data absensi terpilih.
                    </p>
                    <form id="bulkApproveForm">
                        <div class="mb-3">
                            <label for="bulk_status_verif" class="form-label fw-semibold">Status Verifikasi <span class="text-danger">*</span></label>
                            <select class="form-select" id="bulk_status_verif" name="StatusVerif" required>
                                <option value="Y">Y (Disetujui / Acc & Kunci)</option>
                                <option value="N">N (Ditolak / Revisi & Buka Kunci)</option>
                                <option value="N/A">N/A (Belum Diverifikasi / Buka Kunci)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bulk_catatan" class="form-label fw-semibold">Catatan Leader</label>
                            <textarea class="form-control" id="bulk_catatan" name="Catatan" rows="3" placeholder="Opsional: Isi alasan jika ditolak atau catatan tambahan..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light text-muted" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning text-dark fw-semibold px-4" id="btn_submit_bulk">
                        <i class="ti ti-check me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

