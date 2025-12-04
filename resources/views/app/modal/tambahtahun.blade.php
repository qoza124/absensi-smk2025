<div class="modal fade" id="modal-tambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Data Tahun Ajaran</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ url('/tahun/tambah')}}" method="POST">
          @csrf
          <div class="form-group">
            <label for="name">Tahun Ajaran</label>
            <input type="text" name="tahun" id="tahun" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Tanggal Mulai Ajaran</label>
            <input name="mulai" id="mulai" type="text" class="form-control datepicker">
          </div>
          <div class="form-group">
            <label>Tanggal Akhir Ajaran</label>
            <input name="selesai" id="selesai" type="text" class="form-control datepicker">
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