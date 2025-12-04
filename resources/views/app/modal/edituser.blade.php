{{-- ISI FILE: editkelas.blade.php (SUDAH DIPERBAIKI) --}}
{{-- method_field dan csrf_field ini tidak perlu di sini, sudah ada di dalam form --}}
{{-- {{ method_field('patch') }} --}}
{{-- {{ csrf_field() }} --}}
<div class="modal fade" id="modaledit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ubah Data Pengguna</h5>
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
            <label for="name">Username</label>
            <input type="text" name="username" id="username" class="form-control" value=""> {{-- value dikosongkan --}}
          </div>
          <div class="form-group">
            <label for="name">Nama Pengguna</label>
            <input type="text" name="name" id="name" class="form-control" value=""> {{-- value dikosongkan --}}
          </div>
          <div class="form-group">
            <label for="role">Pilih Peran</label>
            <select class="form-control" name="role" id="role" required>
              <option value="" hidden>Pilih Peran</option>  
              <option value="Admin">Admin</option>
              <option value="Guru">Guru</option>
              <option value="Wali Kelas">Wali Kelas</option>
              <option value="Kesiswaan">Kesiswaan</option>
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