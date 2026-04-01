@extends('layouts.app')

@section('title', 'Upload Dokumen')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dokumen.index') }}">Dokumen</a></li>
<li class="breadcrumb-item active">Upload</li>
@endsection

@push('styles')
<style>
    /* === Global & Layout === */
    .page-title {
        font-weight: 600;
        letter-spacing: -0.02em;
        color: #1e293b;
    }

    /* === Upload Zone Styling (Professional Look) === */
    .upload-zone {
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        background-color: #f8fafc;
        background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='12' ry='12' stroke='%23e2e8f0' stroke-width='3' stroke-dasharray='8%2c 10' stroke-dashoffset='0' stroke-linecap='round'/%3e%3c/svg%3e");
        padding: 2rem 1.5rem;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        min-height: 160px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .upload-zone:hover {
        border-color: #0d9488;
        background-color: #f0fdfa;
        box-shadow: 0 4px 20px -2px rgba(13, 148, 136, 0.15);
        transform: translateY(-2px);
    }

    .upload-zone.drag-over {
        border-color: #0d9488;
        background-color: #ccfbf1;
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1);
        transform: scale(1.02);
    }

    .upload-zone input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .upload-zone input[type="file"]:disabled {
        cursor: not-allowed;
    }

    .upload-content {
        pointer-events: none;
        z-index: 1;
        transition: opacity 0.2s;
    }

    .upload-icon {
        font-size: 2.5rem;
        color: #94a3b8;
        margin-bottom: 0.75rem;
        transition: transform 0.3s, color 0.3s;
    }

    .upload-zone:hover .upload-icon {
        transform: translateY(-5px);
        color: #0d9488;
    }

    /* === File List Preview (Modern Pills) === */
    .file-list {
        width: 100%;
        text-align: left;
        margin-top: 1rem;
        max-height: 150px;
        overflow-y: auto;
        padding-right: 0.5rem;
    }

    /* Custom Scrollbar */
    .file-list::-webkit-scrollbar { width: 4px; }
    .file-list::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .file-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    .file-item {
        background: #fff;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 1rem;
        border-radius: 50px;
        margin-bottom: 0.5rem;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #334155;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .file-item i {
        color: #10b981;
        margin-right: 0.5rem;
    }

    /* === Card & Item Styling === */
    .doc-item {
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        transition: all 0.25s;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .doc-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    /* === Existing Doc Styling (Clean Status) === */
    .existing-doc-card {
        border-left: 4px solid #e2e8f0;
        transition: all 0.2s;
        background: #fff;
        border-radius: 8px;
        padding: 0.75rem;
        margin-top: 0.5rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }

    /* Soft Colors for Status */
    .existing-doc-card.status-approved {
        border-left-color: #10b981;
        background: #f0fdf4;
    }
    .existing-doc-card.status-revisi {
        border-left-color: #f59e0b;
        background: #fffbeb;
    }
    .existing-doc-card.status-pending {
        border-left-color: #64748b;
        background: #f8fafc;
    }

    /* === Form Controls === */
    .form-select, .form-control {
        border-radius: 8px;
        border-color: #e2e8f0;
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-select:focus, .form-control:focus {
        border-color: #0d9488;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
    }

    /* === Professional Button === */
    .btn-primary {
        background-color: #0d9488;
        border-color: #0d9488;
        padding: 0.6rem 1.5rem;
        font-weight: 500;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(13, 148, 136, 0.2);
    }
    .btn-primary:hover {
        background-color: #0f766e;
        border-color: #0f766e;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(13, 148, 136, 0.25);
    }

    /* === Sticky Sidebar === */
    .sidebar-sticky {
        position: sticky;
        top: 20px;
    }

    /* === Alert Styling === */
    .alert-pro {
        border-radius: 10px;
        border: none;
        background-color: #fffbeb;
        color: #92400e;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Soft Badge for Status */
    .badge-soft-success { background-color: #dcfce7; color: #166534; }
    .badge-soft-warning { background-color: #fef3c7; color: #92400e; }
    .badge-soft-secondary { background-color: #f1f5f9; color: #475569; }

</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11"> {{-- Sedikit diperlebar agar lebih lapang --}}

        <div class="mb-4">
            <h4 class="page-title mb-1">Upload Dokumen Baru</h4>
            <p class="text-muted mb-0">Lengkapi dokumen persyaratan akademik Anda dengan mengisi form di bawah ini.</p>
        </div>

        @if($hasApprovedDocs)
        <div class="alert alert-pro alert-dismissible fade show mb-4 d-flex align-items-start" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-5 align-self-center"></i>
            <div>
                <strong>Perhatian:</strong> Beberapa dokumen sudah di-approve. Anda dapat mengupload dokumen baru untuk jenis dokumen yang masih belum lengkap atau perlu revisi.
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-4">
            {{-- Main Form --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-semibold text-dark">
                            <i class="bi bi-cloud-arrow-up me-2 text-teal"></i>Form Upload Multi-Dokumen
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                            @csrf

                            {{-- Selections --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">Semester <span class="text-danger">*</span></label>
                                    <select name="semester_id" id="semesterSelect" class="form-select @error('semester_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Semester --</option>
                                        @foreach($semesterList as $semester)
                                        <option value="{{ $semester->id }}" {{ (old('semester_id', $selectedSemester) == $semester->id) ? 'selected' : '' }}>
                                            {{ $semester->tahun_ajaran }} - {{ $semester->nama_semester }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('semester_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">Mata Kuliah <span class="text-danger">*</span></label>
                                    <select name="mata_kuliah_id" id="mkSelect" class="form-select @error('mata_kuliah_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Mata Kuliah --</option>
                                        @forelse($mataKuliahList as $mk)
                                        <option value="{{ $mk->id }}" data-semester-id="{{ $mk->semester_id }}" {{ (old('mata_kuliah_id', $selectedMk) == $mk->id) ? 'selected' : '' }}>
                                            {{ $mk->kode_mk }} - {{ $mk->nama_mk }}
                                        </option>
                                        @empty
                                        <option disabled>-- Belum ada mata kuliah --</option>
                                        @endforelse
                                    </select>
                                    @error('mata_kuliah_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">Tahap <span class="text-danger">*</span></label>
                                    <select name="tahap_id" id="tahapSelect" class="form-select @error('tahap_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Tahap --</option>

                                    </select>
                                    @error('tahap_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Dynamic Upload Area --}}
                            <div id="jenisContainer">
                                <div class="text-center text-muted py-5 border rounded bg-light border-dashed">
                                    <i class="bi bi-arrow-up-circle fs-1 mb-3 d-block text-secondary opacity-50"></i>
                                    <p class="mb-0">Silakan pilih <strong>Tahap</strong> untuk menampilkan form upload.</p>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4 pt-4 border-top">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload me-2"></i> Proses Upload
                                </button>
                                <a href="{{ route('dokumen.index') }}" class="btn btn-light border">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar (Sticky) --}}
            <div class="col-lg-4">
                <div class="sidebar-sticky">
                    <div id="existingDocsContainer" class="mb-4"></div>

                    <div class="card border-0 shadow-sm rounded-3 bg-white">
                        <div class="card-body p-3">
                            <h6 class="fw-semibold mb-3 text-dark">
                                <i class="bi bi-info-circle me-2 text-primary"></i>Panduan Upload
                            </h6>
                            <ul class="list-unstyled mb-0 small text-secondary">
                                <li class="mb-3 d-flex">
                                    <span class="badge bg-light text-dark me-2 p-2"><i class="bi bi-1-circle-fill text-teal"></i></span>
                                    <span>Pilih <strong>Tahap</strong> untuk melihat jenis dokumen yang dibutuhkan.</span>
                                </li>
                                <li class="mb-3 d-flex">
                                    <span class="badge bg-light text-dark me-2 p-2"><i class="bi bi-2-circle-fill text-teal"></i></span>
                                    <span><strong>Drag & drop</strong> file atau klik area upload.</span>
                                </li>
                                <li class="mb-3 d-flex">
                                    <span class="badge bg-light text-dark me-2 p-2"><i class="bi bi-3-circle-fill text-teal"></i></span>
                                    <span>Format yang didukung: <span class="badge bg-light text-dark">PDF</span> <span class="badge bg-light text-dark">DOC</span> <span class="badge bg-light text-dark">DOCX</span>.</span>
                                </li>
                                <li class="d-flex">
                                    <span class="badge bg-light text-dark me-2 p-2"><i class="bi bi-4-circle-fill text-teal"></i></span>
                                    <span>Maksimal ukuran file: <strong>10MB</strong>.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const tahapSelect = document.getElementById('tahapSelect');
    const jenisContainer = document.getElementById('jenisContainer');
    const existingDocsContainer = document.getElementById('existingDocsContainer');
    const mkSelect = document.getElementById('mkSelect');
    const semesterSelect = document.getElementById('semesterSelect');
    const uploadForm = document.getElementById('uploadForm');

    // Variable untuk tracking dokumen yang ada
    let dokumenByJenisGlobal = {};
    let totalJenisDokumen = 0;
    let isLoadingDocs = false;

    // Fungsi untuk load tahap berdasarkan semester
    function loadTahapBySemester(semesterId) {
        if (!semesterId) {
            tahapSelect.innerHTML = '<option value="">-- Pilih Tahap --</option>';
            renderJenisDokumen();
            return;
        }

        fetch(`{{ route('dokumen.tahap-by-semester') }}?semester_id=${semesterId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const { tahap } = data;

            if (!tahap || tahap.length === 0) {
                tahapSelect.innerHTML = '<option value="">-- Tidak ada tahap untuk semester ini --</option>';
                renderJenisDokumen();
                return;
            }

            const oldVal = '{{ old('tahap_id', $selectedTahap) }}';
            let html = '<option value="">-- Pilih Tahap --</option>';
            tahap.forEach(t => {
                const selected = (oldVal == t.id) ? 'selected' : '';
                const jenis = JSON.stringify(t.jenis_dokumen).replace(/"/g, '&quot;');
                html += `<option value="${t.id}" data-jenis='${JSON.stringify(t.jenis_dokumen)}' ${selected}>${t.nama_tahap} - ${t.deskripsi}</option>`;
            });

            tahapSelect.innerHTML = html;
            renderJenisDokumen();
        })
        .catch(err => {
            console.error('Error loading tahap:', err);
            tahapSelect.innerHTML = '<option value="">-- Error loading data --</option>';
        });
    }

    // Fungsi untuk load mata kuliah berdasarkan semester
    function loadMatakuliahBySemester(semesterId) {
        if (!semesterId) {
            mkSelect.innerHTML = '<option value="">-- Pilih Mata Kuliah --</option>';
            loadExistingDocs();
            return;
        }

        fetch(`{{ route('dokumen.mata-kuliah-by-semester') }}?semester_id=${semesterId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const { mataKuliah, count } = data;

            if (!mataKuliah || mataKuliah.length === 0) {
                mkSelect.innerHTML = '<option value="">-- Tidak ada mata kuliah untuk semester ini --</option>';
                loadExistingDocs();
                return;
            }

            let html = '<option value="">-- Pilih Mata Kuliah --</option>';
            mataKuliah.forEach(mk => {
                const selected = ('{{ old('mata_kuliah_id', $selectedMk) }}' == mk.id) ? 'selected' : '';
                html += `<option value="${mk.id}" ${selected}>${mk.kode_mk} - ${mk.nama_mk}</option>`;
            });

            mkSelect.innerHTML = html;

            if (mkSelect.value) {
                loadExistingDocs();
            }
        })
        .catch(err => {
            console.error('Error loading mata kuliah:', err);
            mkSelect.innerHTML = '<option value="">-- Error loading data --</option>';
        });
    }

    // Fungsi untuk menangani render input file
    function renderJenisDokumen() {
        jenisContainer.innerHTML = '';
        dokumenByJenisGlobal = {};
        totalJenisDokumen = 0;

        const selectedOption = tahapSelect.options[tahapSelect.selectedIndex];
        const jenisData = selectedOption.getAttribute('data-jenis');

        if (!jenisData) {
            existingDocsContainer.innerHTML = '';
            jenisContainer.innerHTML = `
                <div class="text-center text-muted py-5 border rounded bg-light border-dashed">
                    <i class="bi bi-arrow-up-circle fs-1 mb-3 d-block text-secondary opacity-50"></i>
                    <p class="mb-0">Silakan pilih <strong>Tahap</strong> untuk menampilkan form upload.</p>
                </div>`;
            return;
        }

        let jenisDokumenList = [];
        try {
            jenisDokumenList = JSON.parse(jenisData);
        } catch (e) { return; }

        if (jenisDokumenList.length === 0) {
            jenisContainer.innerHTML = '<div class="alert alert-info">Tidak ada jenis dokumen spesifik untuk tahap ini.</div>';
            return;
        }

        const title = document.createElement('h6');
        title.className = 'fw-semibold mb-3 mt-2 text-dark';
        title.innerHTML = '<i class="bi bi-files me-2 text-teal"></i>Jenis Dokumen yang Diperlukan';
        jenisContainer.appendChild(title);

        const grid = document.createElement('div');
        grid.className = 'row g-4';

        totalJenisDokumen = jenisDokumenList.length;

        jenisDokumenList.forEach((jenis, index) => {
            const colDiv = document.createElement('div');
            colDiv.className = 'col-md-6';

            const card = document.createElement('div');
            card.className = 'doc-item p-3';

            const inputId = `file_input_${index}`;

            card.innerHTML = `
                <label class="form-label fw-semibold small mb-2 text-dark">
                    <i class="bi bi-file-earmark-text me-1 text-secondary"></i>${jenis}
                </label>

                <div class="upload-zone" data-target="${inputId}" data-jenis="${jenis}">
                    <input type="file" id="${inputId}" name="file_${index}"
                           class="file-input" accept=".pdf,.doc,.docx" multiple>
                    <input type="hidden" name="jenis_${index}" value="${jenis}">

                    <div class="upload-content">
                        <i class="bi bi-cloud-arrow-up upload-icon d-block"></i>
                        <p class="mb-1 text-secondary fw-medium">Tarik file ke sini</p>
                        <p class="small mb-0 text-muted">atau <span class="text-primary fw-semibold">klik untuk pilih</span></p>
                    </div>

                    <div class="file-list"></div>
                    <div class="existing-files-in-upload"></div>
                </div>
            `;

            colDiv.appendChild(card);
            grid.appendChild(colDiv);
        });

        jenisContainer.appendChild(grid);

        initDragAndDrop();
        loadExistingDocs();
    }

    function initDragAndDrop() {
        const zones = document.querySelectorAll('.upload-zone');

        zones.forEach(zone => {
            const input = zone.querySelector('.file-input');
            const fileList = zone.querySelector('.file-list');
            const content = zone.querySelector('.upload-content');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                zone.addEventListener(eventName, preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                zone.addEventListener(eventName, () => zone.classList.add('drag-over'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                zone.addEventListener(eventName, () => zone.classList.remove('drag-over'), false);
            });

            zone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                input.files = dt.files;
                updateFileListDisplay(input, fileList, content);
            }, false);

            input.addEventListener('change', () => updateFileListDisplay(input, fileList, content));
        });
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function updateFileListDisplay(input, fileListContainer, contentContainer) {
        fileListContainer.innerHTML = '';
        const files = input.files;

        if (files.length > 0) {
            contentContainer.style.opacity = '0.3'; // Fade out icon

            Array.from(files).forEach(file => {
                const item = document.createElement('div');
                item.className = 'file-item';
                item.innerHTML = `<span><i class="bi bi-check-circle-fill"></i>${file.name}</span> <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>`;
                fileListContainer.appendChild(item);
            });
        } else {
            contentContainer.style.opacity = '1';
        }
    }

    function loadExistingDocs() {
        const mkId = mkSelect.value;
        const tahapId = tahapSelect.value;

        if (!mkId || !tahapId) {
            existingDocsContainer.innerHTML = '';
            dokumenByJenisGlobal = {};
            return;
        }

        isLoadingDocs = true;

        fetch(`{{ route('dokumen.check') }}?mata_kuliah_id=${mkId}&tahap_id=${tahapId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            isLoadingDocs = false;

            if (!data || !data.dokumenByJenis) return;

            const { dokumenByJenis, isGkmp, currentUserId } = data;

            dokumenByJenisGlobal = {};
            const uploadZones = document.querySelectorAll('.upload-zone');
            uploadZones.forEach(zone => {
                const jenis = zone.getAttribute('data-jenis');
                dokumenByJenisGlobal[jenis] = dokumenByJenis[jenis] || [];
            });

            uploadZones.forEach(zone => {
                const jenis = zone.getAttribute('data-jenis');
                const input = zone.querySelector('.file-input');
                const existingFilesContainer = zone.querySelector('.existing-files-in-upload');

                existingFilesContainer.innerHTML = '';

                input.disabled = false;
                zone.style.opacity = '1';
                zone.style.pointerEvents = 'auto';

                if (dokumenByJenis[jenis] && dokumenByJenis[jenis].length > 0) {
                    let hasApprovedDoc = false;

                    dokumenByJenis[jenis].forEach(doc => {
                        const statusClass = `status-${doc.status}`;
                        let badgeClass = 'badge-soft-secondary';
                        let statusIcon = 'clock-history';

                        if (doc.status === 'approved') {
                            badgeClass = 'badge-soft-success';
                            statusIcon = 'check-circle-fill';
                            hasApprovedDoc = true;
                        } else if (doc.status === 'revisi') {
                            badgeClass = 'badge-soft-warning';
                            statusIcon = 'arrow-clockwise';
                        }

                        const docDiv = document.createElement('div');
                        docDiv.className = `existing-doc-card ${statusClass}`;

                        // Tampilan status badge yang lebih bersih
                        let badgeHtml = `<span class="badge rounded-pill ${badgeClass} text-uppercase">${doc.status}</span>`;

                        // Tombol aksi
                        let actionHtml = '';
                        if ((isGkmp || currentUserId === doc.uploaded_by) && doc.status !== 'approved') {
                            actionHtml = `<button type="button" class="btn btn-sm btn-light text-danger border-0 p-1" onclick="deleteDokumen(${doc.id})" title="Hapus"><i class="bi bi-trash"></i></button>`;
                        } else if (isGkmp && doc.status === 'approved') {
                            actionHtml = `<button type="button" class="btn btn-sm btn-light text-danger border-0 p-1" onclick="deleteDokumen(${doc.id})" title="Hapus"><i class="bi bi-trash"></i></button>`;
                        }

                        docDiv.innerHTML = `
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-truncate small text-dark mb-2">
                                        <i class="bi bi-file-earmark me-1 text-secondary"></i>${doc.nama_file}
                                    </div>
                                    <div class="d-flex flex-column gap-1">
                                        <small class="text-muted d-block">
                                            <i class="bi bi-calendar-event"></i> ${new Date(doc.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </small>
                                        ${doc.uploader ? `<small class="d-block" style="background: #e3f2fd; padding: 4px 8px; border-radius: 4px; color: #1976d2;"><i class="bi bi-person-circle me-1"></i><strong>${doc.uploader.name}</strong></small>` : ''}
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    ${badgeHtml}
                                    ${actionHtml}
                                </div>
                            </div>
                        `;
                        existingFilesContainer.appendChild(docDiv);
                    });

                    const allApproved = dokumenByJenis[jenis].every(doc => doc.status === 'approved');

                    if (allApproved) {
                        input.disabled = true;
                        zone.style.opacity = '100';
                        zone.style.pointerEvents = 'none';

                        // Ganti pesan di dalam upload content
                        const uploadContent = zone.querySelector('.upload-content');
                        uploadContent.style.display = 'none';

                        const disabledMsg = document.createElement('div');
                        disabledMsg.className = 'text-center py-2';
                        disabledMsg.innerHTML = '<small class="text-success fw-semibold"><i class="bi bi-check2-circle me-1"></i>Dokumen lengkap & disetujui</small>';
                        existingFilesContainer.prepend(disabledMsg);

                    } else if (dokumenByJenis[jenis].some(doc => doc.status === 'revisi')) {
                        const revisiMsg = document.createElement('div');
                        revisiMsg.className = 'alert alert-warning py-1 px-2 mb-0 mt-2 small border-0';
                        revisiMsg.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Perlu revisi / upload ulang';
                        existingFilesContainer.appendChild(revisiMsg);
                    }
                }
            });

            updateExistingDocsSidebar(data.dokumen);
        })
        .catch(err => console.error('Error:', err));
    }

    function updateExistingDocsSidebar(dokumenList) {
        if (!dokumenList || dokumenList.length === 0) {
            existingDocsContainer.innerHTML = '';
            return;
        }

        let html = `
            <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden">
                <div class="card-header bg-light py-2 border-bottom">
                    <h6 class="mb-0 small fw-semibold text-dark"><i class="bi bi-clock-history me-2"></i>Status Dokumen</h6>
                </div>
                <div class="card-body p-0">`; // p-0 for list flush

        dokumenList.forEach(doc => {
            let statusClass = 'badge-soft-secondary';
            if (doc.status === 'approved') statusClass = 'badge-soft-success';
            else if (doc.status === 'revisi') statusClass = 'badge-soft-warning';

            html += `
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom" style="background: ${doc.uploaded_by === currentUserId ? '#f0f4ff' : '#fff'};">
                    <div class="flex-grow-1">
                        <div class="text-truncate small fw-medium text-dark" style="max-width: 150px;">${doc.jenis_dokumen}</div>
                        <div class="text-muted text-truncate" style="font-size: 0.75rem;">${doc.nama_file}</div>
                        <small style="color: #1976d2; margin-top: 4px; display: block;">
                            <i class="bi bi-person-circle"></i> <strong>${doc.uploader ? doc.uploader.name : 'Unknown'}</strong>
                        </small>
                    </div>
                    <span class="badge rounded-pill ${statusClass}" style="white-space: nowrap; margin-left: 8px;">${doc.status}</span>
                </div>`;
        });

        html += '</div></div>';
        existingDocsContainer.innerHTML = html;
    }

    // Event Listeners
    semesterSelect.addEventListener('change', function() {
        loadTahapBySemester(this.value);
        loadMatakuliahBySemester(this.value);
    });
    mkSelect.addEventListener('change', loadExistingDocs);
    tahapSelect.addEventListener('change', renderJenisDokumen);

    function deleteDokumen(id) {
        if (confirm('Yakin ingin menghapus dokumen ini?')) {
            fetch(`{{ url('dokumen') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    loadExistingDocs();
                } else {
                    alert(data.message || 'Gagal menghapus dokumen');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Terjadi kesalahan saat menghapus dokumen');
            });
        }
    }

    uploadForm.addEventListener('submit', function(e) {
        if (isLoadingDocs) {
            e.preventDefault();
            alert('Sedang memuat data dokumen, harap tunggu...');
            return false;
        }

        const fileInputs = jenisContainer.querySelectorAll('.file-input:not(:disabled)');
        let hasNewFile = false;
        fileInputs.forEach(input => {
            if (input.files && input.files.length > 0) hasNewFile = true;
        });

        let needsNewDocument = false;
        let jenisDenganRevisiPending = [];
        let jenisDenganApproved = [];

        if (dokumenByJenisGlobal && typeof dokumenByJenisGlobal === 'object') {
            Object.keys(dokumenByJenisGlobal).forEach(jenis => {
                const docs = dokumenByJenisGlobal[jenis] || [];
                const nonApprovedDocs = docs.filter(doc => doc.status !== 'approved');
                const allApproved = docs.length > 0 && docs.every(doc => doc.status === 'approved');

                if (allApproved) {
                    jenisDenganApproved.push(jenis);
                } else if (nonApprovedDocs.length > 0) {
                    jenisDenganRevisiPending.push(jenis);
                } else if (docs.length === 0) {
                    needsNewDocument = true;
                    jenisDenganRevisiPending.push(jenis);
                }
            });
        } else {
            needsNewDocument = totalJenisDokumen > 0;
        }

        if (!hasNewFile && needsNewDocument) {
            e.preventDefault();
            alert('Harap upload minimal satu file dokumen untuk jenis yang masih kosong atau perlu revisi!');
            return false;
        }
    });

    // Init
    document.addEventListener('DOMContentLoaded', function() {
        if (semesterSelect.value) {
            loadTahapBySemester(semesterSelect.value);
            loadMatakuliahBySemester(semesterSelect.value);
        } else {
            renderJenisDokumen();
        }
    });
</script>
@endpush
