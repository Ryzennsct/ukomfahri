

// SET JAM MASUK
function setWaktuMasuk(){
    const now = new Date();
    document.getElementById('waktuMasuk').value =
    now.toISOString().slice(0,16);
}

document.addEventListener('DOMContentLoaded',function(){

    setWaktuMasuk();

});

// hapus

let urlHapus = '';

function setHapus(url) {
    urlHapus = url;
}

document.getElementById('btnKonfirmasiHapus').addEventListener('click', function () {

    Swal.fire({
        title: 'Menghapus...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    window.location.href = urlHapus;
});


