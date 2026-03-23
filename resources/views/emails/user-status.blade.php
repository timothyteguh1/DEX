<!DOCTYPE html>
<html>
<head>
    <title>Status Akun Diperbarui</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; background-color: #f9f9f9; padding: 20px;">
    
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; border-top: 5px solid #20d981; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h2 style="color: #1a202c; margin-top:0;">Halo, {{ $user->name }}!</h2>
        
        <p>Kami ingin menginformasikan bahwa status pendaftaran akun Anda di <strong>Blockped</strong> telah diperbarui oleh Admin.</p>
        
        <div style="background-color: #f1f5f9; padding: 15px; border-radius: 5px; text-align: center; margin: 20px 0;">
            Status pendaftaran Anda saat ini: <br>
            <strong style="font-size: 22px; color: {{ $user->status == 'approved' ? '#16a34a' : ($user->status == 'rejected' || $user->status == 'failed' ? '#dc2626' : '#d97706') }};">
                {{ strtoupper($user->status) }}
            </strong>
        </div>

        @if($user->status == 'approved')
            <p>Selamat! Pembayaran Anda telah dikonfirmasi dan akun Anda telah <strong>disetujui</strong>. Anda sekarang dapat masuk dan mengakses Terminal Trading Blockped.</p>
            <div style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
                <a href="{{ url('/login') }}" style="background-color: #20d981; color: #000; padding: 12px 25px; text-decoration: none; font-weight: bold; border-radius: 5px;">Login ke Blockped</a>
            </div>
        @elseif($user->status == 'rejected')
            <p>Mohon maaf, pendaftaran Anda <strong>ditolak</strong>. Hal ini biasanya terjadi jika bukti pembayaran tidak valid atau tidak jelas. Jika Anda merasa ini adalah kesalahan, silakan hubungi Admin kami.</p>
        @elseif($user->status == 'failed')
            <p>Pendaftaran Anda ditandai <strong>gagal (failed)</strong> karena melebihi batas waktu konfirmasi atau email belum diverifikasi. Silakan mendaftar ulang jika Anda masih ingin bergabung.</p>
        @else
            <p>Akun Anda telah dikembalikan ke status <strong>menunggu (pending)</strong>. Tim kami sedang meninjau kembali data Anda.</p>
        @endif

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">

        <p style="margin-top: 20px; font-size: 13px; color: #64748b;">
            Terima kasih,<br>
            Tim Blockped
        </p>
    </div>

</body>
</html>