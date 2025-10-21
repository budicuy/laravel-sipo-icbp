@extends('layouts.app')

@section('title', 'Edit External Employee')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit External Employee</h3>
                    <div class="card-tools">
                        <a href="{{ route('external-employee.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('external-employee.update', $externalEmployee->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nik_employee">NIK Employee <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nik_employee') is-invalid @enderror" 
                                           id="nik_employee" name="nik_employee" value="{{ old('nik_employee', $externalEmployee->nik_employee) }}" 
                                           placeholder="Masukkan NIK Employee" required>
                                    @error('nik_employee')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="nama_employee">Nama Employee <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_employee') is-invalid @enderror" 
                                           id="nama_employee" name="nama_employee" value="{{ old('nama_employee', $externalEmployee->nama_employee) }}" 
                                           placeholder="Masukkan Nama Employee" required>
                                    @error('nama_employee')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="kode_rm">Kode RM <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kode_rm') is-invalid @enderror" 
                                           id="kode_rm" name="kode_rm" value="{{ old('kode_rm', $externalEmployee->kode_rm) }}" 
                                           placeholder="Masukkan Kode RM" required>
                                    @error('kode_rm')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                           id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $externalEmployee->tanggal_lahir->format('Y-m-d')) }}" required>
                                    @error('tanggal_lahir')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jenis_kelamin') is-invalid @enderror" 
                                            id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" {{ old('jenis_kelamin', $externalEmployee->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $externalEmployee->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="id_vendor">Vendor <span class="text-danger">*</span></label>
                                    <select class="form-control @error('id_vendor') is-invalid @enderror" 
                                            id="id_vendor" name="id_vendor" required>
                                        <option value="">Pilih Vendor</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id_vendor }}" 
                                                    {{ old('id_vendor', $externalEmployee->id_vendor) == $vendor->id_vendor ? 'selected' : '' }}>
                                                {{ $vendor->nama_vendor }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_vendor')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_kategori">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-control @error('id_kategori') is-invalid @enderror" 
                                            id="id_kategori" name="id_kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($kategoris as $kategori)
                                            <option value="{{ $kategori->id_kategori }}" 
                                                    {{ old('id_kategori', $externalEmployee->id_kategori) == $kategori->id_kategori ? 'selected' : '' }}>
                                                {{ $kategori->nama_kategori }} ({{ $kategori->kode_kategori }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_kategori')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="no_hp">No. HP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('no_hp') is-invalid @enderror" 
                                           id="no_hp" name="no_hp" value="{{ old('no_hp', $externalEmployee->no_hp) }}" 
                                           placeholder="Masukkan No. HP" required>
                                    @error('no_hp')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="no_ktp">No. KTP</label>
                                    <input type="text" class="form-control @error('no_ktp') is-invalid @enderror" 
                                           id="no_ktp" name="no_ktp" value="{{ old('no_ktp', $externalEmployee->no_ktp) }}" 
                                           placeholder="Masukkan No. KTP">
                                    @error('no_ktp')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="bpjs_id">BPJS ID</label>
                                    <input type="text" class="form-control @error('bpjs_id') is-invalid @enderror" 
                                           id="bpjs_id" name="bpjs_id" value="{{ old('bpjs_id', $externalEmployee->bpjs_id) }}" 
                                           placeholder="Masukkan BPJS ID">
                                    @error('bpjs_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="aktif" {{ old('status', $externalEmployee->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="nonaktif" {{ old('status', $externalEmployee->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="foto">Foto</label>
                                    <input type="file" class="form-control @error('foto') is-invalid @enderror" 
                                           id="foto" name="foto" accept="image/*">
                                    @error('foto')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">Format: JPEG, PNG, JPG. Max: 2MB</small>
                                    @if($externalEmployee->foto)
                                        <div class="mt-2">
                                            <small>Foto saat ini:</small><br>
                                            <img src="{{ asset('storage/' . $externalEmployee->foto) }}" 
                                                 alt="Foto" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alamat">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                      id="alamat" name="alamat" rows="3" placeholder="Masukkan Alamat" required>{{ old('alamat', $externalEmployee->alamat) }}</textarea>
                            @error('alamat')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="{{ route('external-employee.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection