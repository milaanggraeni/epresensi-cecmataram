<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\WaNotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiToken;

    public function __construct()
    {
        $this->apiUrl = config('whatsapp.api_url');
        $this->apiToken = config('whatsapp.api_token');
    }

    public function sendAbsensiNotification(Absensi $absensi)
    {
        $peserta = $absensi->peserta;
        $jadwal = $absensi->jadwal;

        if (!$peserta->nomor_hp_wali) {
            return false;
        }

        $message = $this->formatMessage($absensi);
        return $this->send($peserta->nomor_hp_wali, $message, $absensi->id);
    }

    protected function formatMessage(Absensi $absensi)
    {
        $peserta = $absensi->peserta;
        $jadwal = $absensi->jadwal;
        $kelas = $jadwal->kelas;
        
        $jamMasuk = date('H:i', strtotime($absensi->jam_masuk));
        $tanggal = \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->isoFormat('D MMMM Y');

        return "✅ *E-Presensi CEC Mataram*

Yth. Bapak/Ibu Wali dari *" . $peserta->nama . "*,

Putra/Putri Anda telah hadir di kelas:
📚 Mata Pelajaran: " . $jadwal->mata_pelajaran . "
🏫 Kelas: " . ($kelas->nama_kelas ?? '-') . "
🕐 Jam: " . \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') . " - " . \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') . "
📅 Tanggal: " . $tanggal . "
⏰ Waktu Scan: " . $jamMasuk . "

Terima kasih atas perhatiannya.
— CEC Mataram";
    }

    public function send(string $phone, string $message, $absensiId = null)
    {
        if (!$this->apiToken || !$this->apiUrl) {
            Log::warning('WhatsApp API token or URL is not configured.');
            return false;
        }

        // Format phone number (e.g. replace leading 0 with 62)
        $phone = preg_replace('/^0/', '62', $phone);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiToken
            ])->post($this->apiUrl, [
                'target' => $phone,
                'message' => $message,
            ]);

            $status = $response->successful() ? 'sent' : 'failed';
            
            if ($absensiId) {
                WaNotificationLog::create([
                    'absensi_id' => $absensiId,
                    'nomor_tujuan' => $phone,
                    'pesan' => $message,
                    'status' => $status,
                    'response' => $response->body()
                ]);
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp Notification Error: ' . $e->getMessage());
            
            if ($absensiId) {
                WaNotificationLog::create([
                    'absensi_id' => $absensiId,
                    'nomor_tujuan' => $phone,
                    'pesan' => $message,
                    'status' => 'failed',
                    'response' => $e->getMessage()
                ]);
            }
            
            return false;
        }
    }
}
