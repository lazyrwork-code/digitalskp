@extends('layouts.app')
@section('title', 'Repository SKP')
@section('content')
<div class="content">
    <h3 class="fw-bold mb-4">Repository SKP</h3>
    <div class="bg-white p-4 rounded-4">
        <div class="d-flex justify-content-end mb-4">
            <div class="input-group" style="width: 320px">
                <input type="text" id="searchInput" class="form-control" placeholder="Ketikkan nama...." />
                <span class="input-group-text bg-white">
                    <i class="bi bi-search text-muted"></i>
                </span>
            </div>
        </div>
        <table class="table align-middle" id="repositoryTable">
            <thead>
                <tr>
                    <th>Nama Pegawai</th>
                    <th>Unit</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td>{{ $item->user->nama }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="text-center">
                        <a class="btn btn-outline-success btn-sm"
                           href="{{ route('admin.riwayat-user', $item->user->id) }}">
                            Lihat SKP
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
<script>
document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#repositoryTable tbody tr");
    let found = false;
    rows.forEach(function(row) {
        let text = row.textContent.toLowerCase();
        if (text.includes(filter)) { row.style.display = ""; found = true; }
        else row.style.display = "none";
    });
    let tbody = document.querySelector("#repositoryTable tbody");
    let emptyRow = document.getElementById("noDataRow");
    if (!found) {
        if (!emptyRow) {
            let tr = document.createElement("tr");
            tr.id = "noDataRow";
            tr.innerHTML = `<td colspan="3" class="text-center text-muted">Data tidak ditemukan</td>`;
            tbody.appendChild(tr);
        }
    } else { if (emptyRow) emptyRow.remove(); }
});
</script>