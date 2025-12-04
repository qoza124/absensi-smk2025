<div class="modal fade" id="modal-tambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Data Pengguna</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ url('/user/tambah')}}" method="POST">
          @csrf
          <div class="form-group">
            <label for="username">Username Pengguna</label>
            <input type="text" name="username" id="username" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="name">Nama Pengguna</label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="users_id">-- Pilih Peran Pengguna --</label>
            <select class="form-control" name="role" id="role" required>
              <option value="" hidden>Pilih Peran</option>  
              <option value="Admin">Admin</option>
              <option value="Guru">Guru</option>
              <option value="Wali Kelas">Wali Kelas</option>
              <option value="Kesiswaan">Kesiswaan</option>
            </select>
          </div>
          <div class="form-group">
            <label for="name">Password</label>
            <input type="text" name="password" id="password" value="123456*" class="form-control" required>
          </div>
          <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-primary" type="submit">Simpan Data</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>