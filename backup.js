

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    loadKendaraanList();
    loadAreaList();
    setWaktuMasuk();
    loadParkirAktif();
    loadRekapTransaksi();
    setupEventListeners();
});

// Load daftar kendaraan ke dropdown
function loadKendaraanList() {
    fetch('get_kendaraan.php?action=list')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('selectKendaraan');
            select.innerHTML = '<option value="">-- Pilih Kendaraan --</option>';
            
            data.forEach(kendaraan => {
                const option = document.createElement('option');
                option.value = kendaraan.id_kendaraan;
                option.textContent = `${kendaraan.plat_nomor} - ${kendaraan.jenis} (${kendaraan.pemilik})`;
                option.dataset.plat = kendaraan.plat_nomor;
                option.dataset.jenis = kendaraan.jenis;
                option.dataset.warna = kendaraan.warna;
                option.dataset.pemilik = kendaraan.pemilik;
                option.dataset.status = kendaraan.status_parkir;
                
                if (kendaraan.status_parkir === 'parkir') {
                    option.textContent += ' 🅿️';
                    option.style.color = '#ff9800';
                    option.style.fontWeight = 'bold';
                }
                
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error:', error));
}

// Load area parkir
function loadAreaList() {
    fetch('get_area.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('selectArea');
            select.innerHTML = '<option value="">-- Pilih Area --</option>';
            
            data.forEach(area => {
                const option = document.createElement('option');
                option.value = area.id_area;
                option.textContent = `${area.nama_area} (Rp ${parseInt(area.tarif_perjam).toLocaleString('id-ID')}/jam)`;
                option.dataset.tarif = area.tarif_perjam;
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error:', error));
}

// Set waktu masuk otomatis
function setWaktuMasuk() {
    const now = new Date();
    const datetime = now.toISOString().slice(0, 16);
    document.getElementById('waktuMasuk').value = datetime;
}

// Setup event listeners
function setupEventListeners() {
    // Kendaraan dipilih
    document.getElementById('selectKendaraan').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (this.value) {
            document.getElementById('infoKendaraan').style.display = 'block';
            document.getElementById('info-plat').textContent = option.dataset.plat;
            document.getElementById('info-jenis').textContent = option.dataset.jenis;
            document.getElementById('info-warna').textContent = option.dataset.warna;
            document.getElementById('info-pemilik').textContent = option.dataset.pemilik;
            
            // Cek status parkir
            if (option.dataset.status === 'parkir') {
                document.getElementById('alertParkir').style.display = 'block';
                document.getElementById('textAlertParkir').textContent = 
                    'Kendaraan ini sedang parkir. Pilih status "Keluar" untuk memproses pembayaran.';
                document.getElementById('selectStatus').value = 'keluar';
                toggleFieldsByStatus('keluar');
            } else {
                document.getElementById('alertParkir').style.display = 'none';
            }
        } else {
            document.getElementById('infoKendaraan').style.display = 'none';
        }
    });

    // Status berubah
    document.getElementById('selectStatus').addEventListener('change', function() {
        toggleFieldsByStatus(this.value);
    });

    // Waktu keluar berubah
    document.getElementById('waktuKeluar').addEventListener('change', hitungBiaya);
    document.getElementById('selectArea').addEventListener('change', hitungBiaya);

    // Form submit
    document.getElementById('formTransaksi').addEventListener('submit', function(e) {
        e.preventDefault();
        simpanTransaksi();
    });

    // Filter waktu
    document.getElementById('filterWaktu').addEventListener('change', function() {
        const val = this.value;
        if (val === 'custom') {
            document.getElementById('groupTanggalMulai').style.display = 'block';
            document.getElementById('groupTanggalAkhir').style.display = 'block';
        } else {
            document.getElementById('groupTanggalMulai').style.display = 'none';
            document.getElementById('groupTanggalAkhir').style.display = 'none';
            loadRekapTransaksi();
        }
    });

    document.getElementById('btnCariRekap').addEventListener('click', loadRekapTransaksi);
}

// Toggle fields berdasarkan status
function toggleFieldsByStatus(status) {
    const groupKeluar = document.getElementById('groupWaktuKeluar');
    const detailPembayaran = document.getElementById('detailPembayaran');
    
    if (status === 'keluar') {
        groupKeluar.style.display = 'block';
        detailPembayaran.style.display = 'block';
        
        const now = new Date();
        document.getElementById('waktuKeluar').value = now.toISOString().slice(0, 16);
        hitungBiaya();
    } else {
        groupKeluar.style.display = 'none';
        detailPembayaran.style.display = 'none';
    }
}

// Hitung biaya
function hitungBiaya() {
    const waktuMasuk = new Date(document.getElementById('waktuMasuk').value);
    const waktuKeluar = new Date(document.getElementById('waktuKeluar').value);
    const areaSelect = document.getElementById('selectArea');
    
    if (!waktuMasuk || !waktuKeluar || !areaSelect.value) return;
    
    const diff = waktuKeluar - waktuMasuk;
    const jam = Math.ceil(diff / (1000 * 60 * 60));
    const durasi = jam < 1 ? 1 : jam;
    
    const tarif = parseInt(areaSelect.options[areaSelect.selectedIndex].dataset.tarif);
    const biaya = durasi * tarif;
    
    document.getElementById('durasiJam').value = durasi;
    document.getElementById('tarifPerJam').value = 'Rp ' + tarif.toLocaleString('id-ID');
    document.getElementById('biayaTotal').value = 'Rp ' + biaya.toLocaleString('id-ID');
}

// Simpan transaksi
function simpanTransaksi() {
    const formData = new FormData(document.getElementById('formTransaksi'));
    
    fetch('proses_transaksi.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('✅ ' + data.message);
            
            // Cetak struk
            window.open(`cetak_struk.php?id=${data.id_transaksi}`, '_blank', 'width=400,height=700');
            
            // Reset form
            document.getElementById('formTransaksi').reset();
            document.getElementById('infoKendaraan').style.display = 'none';
            setWaktuMasuk();
            
            // Reload data
            loadKendaraanList();
            loadParkirAktif();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan saat menyimpan transaksi');
    });
}

// Load parkir aktif
function loadParkirAktif() {
    fetch('get_transaksi.php?type=aktif')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tabelParkirAktif');
            tbody.innerHTML = '';
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">Tidak ada kendaraan yang parkir</td></tr>';
                return;
            }
            
            data.forEach((item, index) => {
                const masuk = new Date(item.waktu_masuk);
                const now = new Date();
                const durasi = Math.ceil((now - masuk) / (1000 * 60 * 60));
                
                const row = `<tr>
                    <td>${index + 1}</td>
                    <td><span class="badge bg-warning text-dark">${item.plat_nomor}</span></td>
                    <td>${item.jenis}</td>
                    <td>${item.pemilik}</td>
                    <td>${item.nama_area}</td>
                    <td>${masuk.toLocaleString('id-ID')}</td>
                    <td>${durasi} jam</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="prosesKeluar(${item.id_kendaraan})">
                            <i class="fas fa-sign-out-alt"></i> Keluar
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += row;
            });
        })
        .catch(error => console.error('Error:', error));
}

// Load rekap transaksi
function loadRekapTransaksi() {
    const filter = document.getElementById('filterWaktu').value;
    const mulai = document.getElementById('tanggalMulai').value;
    const akhir = document.getElementById('tanggalAkhir').value;
    
    let url = `rekap_transaksi.php?action=get&filter=${filter}`;
    if (filter === 'custom' && mulai && akhir) {
        url += `&tanggal_mulai=${mulai}&tanggal_akhir=${akhir}`;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(result => {
            const tbody = document.getElementById('tabelRekap');
            tbody.innerHTML = '';
            
            if (result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="11" class="text-center">Tidak ada data</td></tr>';
                return;
            }
            
            result.data.forEach((item, index) => {
                const statusBadge = item.status === 'masuk' ? 
                    '<span class="badge bg-success">Parkir</span>' : 
                    '<span class="badge bg-primary">Selesai</span>';
                    
                const row = `<tr>
                    <td>${index + 1}</td>
                    <td>${item.plat_nomor}</td>
                    <td>${item.jenis}</td>
                    <td>${item.pemilik}</td>
                    <td>${item.nama_area}</td>
                    <td>${new Date(item.waktu_masuk).toLocaleString('id-ID', {dateStyle: 'short', timeStyle: 'short'})}</td>
                    <td>${item.waktu_keluar ? new Date(item.waktu_keluar).toLocaleString('id-ID', {dateStyle: 'short', timeStyle: 'short'}) : '-'}</td>
                    <td>${item.durasi_jam || '-'} jam</td>
                    <td>Rp ${item.biaya_total ? parseInt(item.biaya_total).toLocaleString('id-ID') : '0'}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="cetakStruk(${item.id_parkir})">
                            <i class="fas fa-print"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += row;
            });
            
            // Update statistik
            document.getElementById('statTotalTransaksi').textContent = result.statistik.total_transaksi;
            document.getElementById('statTotalPendapatan').textContent = 'Rp ' + parseInt(result.statistik.total_pendapatan).toLocaleString('id-ID');
            document.getElementById('statParkirAktif').textContent = result.statistik.total_kendaraan_parkir;
        })
        .catch(error => console.error('Error:', error));
}

// Proses keluar
function prosesKeluar(idKendaraan) {
    document.getElementById('selectKendaraan').value = idKendaraan;
    document.getElementById('selectKendaraan').dispatchEvent(new Event('change'));
    document.getElementById('selectStatus').value = 'keluar';
    toggleFieldsByStatus('keluar');
    
    // Scroll ke form
    document.getElementById('sistem-parkir').scrollIntoView({behavior: 'smooth'});
    document.getElementById('transaksi-tab').click();
}

// Cetak struk
function cetakStruk(idParkir) {
    window.open(`cetak_struk.php?id=${idParkir}`, '_blank', 'width=400,height=700');
}

// Auto refresh setiap 30 detik
setInterval(() => {
    loadParkirAktif();
}, 30000);
let idAreaHapus = null;

function hapusArea(idArea, namaArea) {
    // Set data ke modal
    idAreaHapus = idArea;
    document.getElementById('namaAreaHapus').textContent = namaArea;
    
    // Tampilkan modal
    const modal = new bootstrap.Modal(document.getElementById('modalHapusArea'));
    modal.show();
}

// Event listener untuk tombol konfirmasi hapus
document.getElementById('btnKonfirmasiHapus').addEventListener('click', function() {
    if (idAreaHapus !== null) {
        // Redirect ke halaman hapus atau kirim request AJAX
        window.location.href = 'hapus_area.php?id=' + idAreaHapus;
        
        // Atau jika menggunakan AJAX:
        /*
        fetch('hapus_area.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + idAreaHapus
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tutup modal
                bootstrap.Modal.getInstance(document.getElementById('modalHapusArea')).hide();
                // Reload halaman atau update tampilan
                location.reload();
            } else {
                alert('Gagal menghapus area: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus area');
        });
        */
    }
});

// Fungsi untuk hapus kendaraan
let idKendaraanHapus = null;

function hapusKendaraan(idKendaraan, platNomor) {
    // Set data ke modal
    idKendaraanHapus = idKendaraan;
    document.getElementById('platNomorHapus').textContent = platNomor;
    
    // Tampilkan modal
    const modal = new bootstrap.Modal(document.getElementById('modalHapusKendaraan'));
    modal.show();
}

// Event listener untuk tombol konfirmasi hapus
document.getElementById('btnKonfirmasiHapusKendaraan').addEventListener('click', function() {
    if (idKendaraanHapus !== null) {
        // Redirect ke halaman hapus
        window.location.href = 'proses_hapus_kendaraan.php?id=' + idKendaraanHapus;
        
        // Atau jika menggunakan AJAX:
        /*
        fetch('proses_hapus_kendaraan.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + idKendaraanHapus
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tutup modal
                bootstrap.Modal.getInstance(document.getElementById('modalHapusKendaraan')).hide();
                // Reload halaman
                location.reload();
            } else {
                alert('Gagal menghapus kendaraan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus kendaraan');
        });
        */
    }
});

// Validasi form tambah kendaraan
document.getElementById('formTambahKendaraan').addEventListener('submit', function(e) {
    const platNomor = document.getElementById('platNomor').value.trim();
    const jenisKendaraan = document.getElementById('jenisKendaraan').value;
    
    if (platNomor === '') {
        e.preventDefault();
        alert('Plat Nomor harus diisi!');
        return false;
    }
    
    if (jenisKendaraan === '') {
        e.preventDefault();
        alert('Jenis Kendaraan harus dipilih!');
        return false;
    }
});

// Counter Animation + Format Ribuan
document.addEventListener("DOMContentLoaded", function () {

    const counters = document.querySelectorAll('.counter');
  
    counters.forEach(counter => {
  
      const target = +counter.getAttribute('data-target');
      let count = 0;
  
      const speed = 50;
  
      // Format angka pakai titik
      function formatNumber(num){
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      }
  
      const updateCount = () => {
  
        const inc = target / speed;
  
        if (count < target) {
  
          count += inc;
  
          counter.innerText = formatNumber(Math.ceil(count));
  
          setTimeout(updateCount, 20);
  
        } else {
  
          counter.innerText = formatNumber(target);
  
        }
      };
  
      updateCount();
    });
  
  });
  
  
  