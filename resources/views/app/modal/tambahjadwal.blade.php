<div class="modal fade" id="modal-tambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Jadwal Mengajar</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ url('/jadwal/tambah')}}" method="POST">
          @csrf
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
            <select class="form-control" name="jam_selesai" id="jam_selesai" required disabled>
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
      </div>
    </div>
  </div>
</div>

@push('script')
<script>
$(document).ready(function() {
    
    // 1. Definisikan nilai maksimum Anda
    var nilaiMax = 10; 

    // 2. Buat event listener untuk dropdown pertama
    $('#jam_mulai').on('change', function() {
        
        // 3. Ambil nilai yang dipilih dari dropdown pertama
        // Kita gunakan parseInt() untuk mengubahnya jadi Angka
        var nilaiMulai = parseInt($(this).val());
        
        // 4. Target dropdown kedua
        var dropdownSelesai = $('#jam_selesai');
        
        // 5. Bersihkan (kosongkan) opsi di dropdown kedua
        dropdownSelesai.empty();

        // 6. Cek apakah pengguna memilih angka yang valid
        if (nilaiMulai) {
            
            // 7. Jika valid, aktifkan dropdown kedua
            dropdownSelesai.prop('disabled', false);
            
            // 8. Tambahkan opsi placeholder baru
            dropdownSelesai.append('<option value="" hidden>-- Pilih Jam Selesai Mengajar --</option>');
            
            // 9. Lakukan loop dari nilaiMulai s/d nilaiMax
            for (var i = nilaiMulai; i <= nilaiMax; i++) {
                // 10. Tambahkan setiap angka sebagai <option> baru
                dropdownSelesai.append('<option value="' + i + '">' + i + '</option>');
            }

        } else {
            // 11. Jika pengguna memilih "-- Pilih --" (tidak valid)
            // Tambahkan placeholder disabled
            dropdownSelesai.append('<option value="">-- Pilih Jam Mulai Mengajar Terlebih Dahulu --</option>');
            // Dan non-aktifkan lagi dropdown kedua
            dropdownSelesai.prop('disabled', true);
        }
    });

});
</script>
@endpush
