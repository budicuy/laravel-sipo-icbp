@extends('layouts.app')

@section('title', 'Detail External Employee')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail External Employee</h3>
                        <div class="card-tools">
                            <a href="{{ route('external-employee.edit', $externalEmployee->id_external_employee) }}"
                                class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('external-employee.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                @if ($externalEmployee->foto)
                                    <div class="text-center">
                                        <img src="{{ asset('storage/' . $externalEmployee->foto) }}"
                                            alt="Foto {{ $externalEmployee->nama_employee }}" class="img-fluid rounded"
                                            style="max-width: 250px;">
                                    </div>
                                @else
                                    <div class="text-center">
                                        <div class="bg-gray-200 d-flex align-items-center justify-content-center rounded"
                                            style="width: 250px; height: 250px; margin: 0 auto;">
                                            <i class="fas fa-user fa-5x text-gray-400"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="25%">NIK Employee</th>
                                        <td>{{ $externalEmployee->nik_employee }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Employee</th>
                                        <td>{{ $externalEmployee->nama_employee }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kode RM</th>
                                        <td>{{ $externalEmployee->kode_rm }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Lahir</th>
                                        <td>{{ $externalEmployee->tanggal_lahir->format('d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Jenis Kelamin</th>
                                        <td>{{ $externalEmployee->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                    </tr>
                                    <tr>
                                        <th>No. HP</th>
                                        <td>{{ $externalEmployee->no_hp }}</td>
                                    </tr>
                                    <tr>
                                        <th>Vendor</th>
                                        <td>{{ $externalEmployee->vendor->nama_vendor }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kategori</th>
                                        <td>
                                            <span
                                                class="badge badge-{{ $externalEmployee->kategori->kode_kategori == 'x' ? 'warning' : ($externalEmployee->kategori->kode_kategori == 'y' ? 'info' : 'success') }}">
                                                {{ $externalEmployee->kategori->nama_kategori }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>No. KTP</th>
                                        <td>{{ $externalEmployee->no_ktp ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>BPJS ID</th>
                                        <td>{{ $externalEmployee->bpjs_id ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span
                                                class="badge badge-{{ $externalEmployee->status == 'aktif' ? 'success' : 'danger' }}">
                                                {{ $externalEmployee->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Alamat</th>
                                        <td>{{ $externalEmployee->alamat }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="btn-group">
                                    <a href="{{ route('external-employee.edit', $externalEmployee->id_external_employee) }}"
                                        class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit Data
                                    </a>
                                    <form
                                        action="{{ route('external-employee.destroy', $externalEmployee->id_external_employee) }}"
                                        method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            <i class="fas fa-trash"></i> Hapus Data
                                        </button>
                                    </form>
                                    <a href="{{ route('external-employee.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
