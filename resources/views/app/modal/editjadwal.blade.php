{{-- ISI FILE: editkelas.blade.php (SUDAH DIPERBAIKI) --}}
{{-- method_field dan csrf_field ini tidak perlu di sini, sudah ada di dalam form --}}
{{-- {{ method_field('patch') }} --}}
{{-- {{ csrf_field() }} --}}
<div class="modal fade" id="modaledit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Data Kelas</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{-- PERUBAHAN PADA FORM DI BAWAH INI --}}
        <form id="editForm" action="" method="POST">
          @csrf
          @method('PUT')
          <div class="form-group">
            <label for="tahun_id">Tahun Ajaran</label>
            <select class="form-control" name="tahun_id" id="tahun_id" required>
              @foreach ($tahun as $item)
              <option value="" hidden>-- Pilih Tahun Ajaran --</option>  
              <option value="{{ $item->id }}">{{ $item->tahun }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="users_id">Nama Guru</label>
            <select class="form-control" name="users_id" id="users_id" required>
              @foreach ($users as $item)
              <option value="" hidden>-- Pilih Guru --</option>  
              <option value="{{ $item->id }}">{{ $item->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="kelas_id">Kelas</label>
            <select class="form-control" name="kelas_id" id="kelas_id" required>
              @foreach ($kelas as $item)
              <option value="" hidden>-- Pilih Kelas --</option>  
              <option value="{{ $item->id }}">{{ $item->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="mapel_id">Mata Pelajaran</label>
            <select class="form-control" name="mapel_id" id="mapel_id" required>
              @foreach ($mapel as $item)
              <option value="" hidden>-- Pilih Mata Pelajaran --</option>  
              <option value="{{ $item->id }}">{{ $item->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="hari">Hari Mengajar</label>
            <select class="form-control" name="hari" id="hari" required>
              <option value="" hidden>-- Pilih Hari Mengajar --</option>  
              <option value="Senin">Senin</option>
              <option value="Selasa">Selasa</option>
              <option value="Rabu">Rabu</option>
              <option value="Kamis">Kamis</option>
              <option value="Jumat">Jumat</option>
            </select>
          </div>
          <div class="form-group">
            <label for="jam_mulai">Jam Mulai Mengajar</label>
            <select class="form-control" name="jam_mulai" id="jam_mulai" required>
              <option value="" hidden>-- Pilih Jam Mulai Mengajar --</option>  
              @for($i=1; $i<=10;$i++)
              <option value="{{ $i }}">{{ $i }}</option>
              @endfor
            </select>
          </div>
          <div class="form-group">
            <label for="jam_selesai">Jam Selesai Mengajar</label>
            <select class="form-control" name="jam_selesai" id="jam_selesai" required>
              <option value="" hidden>-- Pilih Jam Selesai Mengajar --</option>  
              @for($i=1; $i<=10;$i++)
              <option value="{{ $i }}">{{ $i }}</option>
              @endfor
            </select>
          </div>
          <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-primary" type="submit">Simpan Data</button>
          </div>
        </form>
        {{-- BATAS PERUBAHAN FORM --}}
      </div>
    </div>
  </div>
</div>