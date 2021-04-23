<?php
error_reporting(0);
include 'config.php';
class NakoSec
{
    public function banner(){
        echo " ==========================================\n";
        echo " Nako Open Source Information (NOSINT) 1.3\n";
        echo " Made with {coffee} and {heart} by Nicsx\n";
        echo " ==========================================\n";
    }
    public function getAlamat($nik)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://pencarian.bataraguru.id/search-data/ktp/alamat?nik=' . $nik);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: msnbot/1.1'
        ));
        $nako = curl_exec($ch);
        curl_close($ch);
        $x = json_decode($nako, true);
        $debug = $x['full_address'];
        return $debug;
    }
    public function nama($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: msnbot/1.1'
        ));
        $nako = curl_exec($ch);
        curl_close($ch);
        $x = json_decode($nako, true);
        for ($c = 0;$c < 25;$c++)
        {
            $debug = $x['persons'][$c];
            if ($debug == "")
            {
                echo "\nTIDAK ADA HASIL LAIN!\n";
                exit();
            }
            else
            {
                $nama = $debug['nama'];
                $nik = $debug['nik'];
                $nkk = $debug['nkk'];
                $gender = $debug['jenis_kelamin'];
                $stat_kawin = $debug['kawin'];
                $pekerjaan = $debug['pekerjaan'];
                $alamat = $debug['alamat'];
                $rt = $debug['rt'];
                $rw = $debug['rw'];
                $tempat_lahir = $debug['tempat_lahir'];
                $ttl = $debug['tanggal_lahir'];
                $parsing_umur = explode("-", $ttl);
                $umur = (date('Y')-$parsing_umur[0]);
                $output = "[ Hasil Pencarian Dari " . $nama . " ]
NIK: " . $nik . "
NKK: " . $nkk . "
Nama: " . $nama . "
Tempat Lahir: " . $tempat_lahir . "
Alamat Lengkap: " . $alamat . " RT0" . $rt . "/RW0" . $rw . ", " . $this->getAlamat($nik) . "
Umur: ".$umur."
Jenis Kelamin: " . $gender . "
Pekerjaan: " . $pekerjaan . "
Status Perkawinan: " . $stat_kawin . "
Tanggal Lahir: " . $ttl . "\n";
                echo $output;
                if (!file_exists('tmp'))
                {
                    mkdir('tmp', 0777, true);
                } else {
                    file_put_contents('tmp/data-' . $nama . '.txt', $output, FILE_APPEND);
                }
            }
        }
    }
    public function osint()
    {
        echo "1.Search by Name\n2.Search by NIK\n3.Search by NKK\nChoose option: ";
        $search = trim(fgets(STDIN));
        switch ($search) {
            case "1":
                echo "Input Nama: ";
                $nama = trim(fgets(STDIN));
                echo "Tempat Lahir (isi n/a jika tidak tahu): ";
                $inputlahir = trim(fgets(STDIN));
                if($inputlahir == "n/a"){
                    $this->nama('https://pencarian.bataraguru.id/search-data/ktp/datin/json?limit=100&nama=' . urlencode($nama));
                } else {
                    $this->nama('https://pencarian.bataraguru.id/search-data/ktp/datin/json?limit=100&nama=' . urlencode($nama). '&tempat_lahir='.$inputlahir);
                }
                break;
            case "2":
                echo "Input NIK: ";
                $nik = trim(fgets(STDIN));
                $this->nama('https://pencarian.bataraguru.id/search-data/ktp/datin/json?limit=100&nik=' . urlencode($nik));
                break;
            case "3":
                echo "Input NKK: ";
                $nkk = trim(fgets(STDIN));
                echo "Tempat Lahir (isi n/a jika tidak tahu): ";
                $this->nama('https://pencarian.bataraguru.id/search-data/ktp/datin/json?limit=100&nkk=' . urlencode($nkk));
                break;
            default:
                echo "Wrong option key\n";
        }
    }
}
$core = new NakoSec;
$core->banner();
$core->osint();