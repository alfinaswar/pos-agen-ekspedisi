    <!-- Modal Bulk Verify -->
    <div class="modal fade" id="bulkVerifyModal" tabindex="-1" aria-hidden="true">
        <!-- HAPUS class 'modal-dialog-centered' agar modal muncul di atas -->
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning bg-opacity-10 border-bottom-0">
                    <h5 class="modal-title fw-bold text-warning-emphasis">
                        <i class="ti ti-shield-check me-2"></i>Verifikasi Massal Finance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Anda akan mengubah status untuk <strong id="modal_selected_count" class="text-dark">0</strong> transaksi terpilih.
                    </p>
                    <form id="bulkVerifyForm">
                        <div class="mb-3">
                            <label for="bulk_status" class="form-label fw-semibold">Status Verifikasi <span class="text-danger">*</span></label>
                            <!-- Teks opsi diperjelas, tapi value tetap Y, N, N/A untuk backend -->
                            <select class="form-select" id="bulk_status" name="Status" required>
                                <option value="">Pilih Status</option>
                                <option value="Y">Disetujui / Valid</option>
                                <option value="N">Ditolak / Tidak Valid</option>
                                <option value="N/A">Belum Diverifikasi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bulk_catatan" class="form-label fw-semibold">Catatan Finance</label>
                            <textarea class="form-control" id="bulk_catatan" name="Catatan" rows="3" placeholder="Opsional: Isi catatan jika ada temuan atau alasan penolakan..."></textarea>
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
