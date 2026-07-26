<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Aset - {{ $aset->kode_aset }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Pengaturan ukuran kertas untuk Label Printer (Contoh: 10cm x 6cm) */
        @media print {
            @page { size: 10cm 6cm landscape; margin: 0; }
            body { margin: 0; padding: 0; background: white; }
            .print-btn { display: none !important; }
        }
        body { 
            background: #f3f4f6; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            font-family: Arial, sans-serif; 
        }
        .label-card { 
            width: 10cm; 
            height: 6cm; 
            background: white; 
            border: 3px solid black; 
            padding: 0.5cm; 
            position: relative; 
            box-sizing: border-box; 
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn fixed top-6 right-6 bg-blue-800 hover:bg-blue-900 text-white px-6 py-3 rounded-full font-bold uppercase shadow-2xl tracking-widest text-xs transition">
        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        Cetak Label Barang
    </button>

    {{-- KARTU LABEL ASET --}}
    <div class="label-card flex flex-col justify-between text-black shadow-xl print:shadow-none">
        
        {{-- Header Label --}}
        <div class="flex items-center border-b-2 border-black pb-1 mb-2">
            @if (isset($setting) && $setting->logo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($setting->logo_path) }}" class="h-10 w-auto mr-3">
            @endif
            <div>
                <h1 class="text-[12px] font-black uppercase leading-none tracking-tight">INVENTARIS ASET</h1>
                <h2 class="text-[9px] font-bold uppercase leading-tight mt-0.5 tracking-widest">
                    {{ $aset->jemaat->nama_jemaat ?? ($aset->klasis->nama_klasis ?? 'GPI DI PAPUA') }}
                </h2>
            </div>
        </div>
        
        {{-- Konten Tengah --}}
        <div class="text-center my-auto">
            <h3 class="text-[14px] font-black uppercase leading-tight">{{ $aset->nama_aset }}</h3>
            
            <div class="mt-2">
                <span class="text-[14px] font-mono font-black bg-black text-white px-3 py-1 inline-block tracking-widest">
                    {{ $aset->kode_aset }}
                </span>
            </div>
        </div>

        {{-- Footer Label --}}
        <div class="border-t-2 border-black pt-1.5 flex justify-between text-[8px] font-black uppercase tracking-widest">
            <span>KAT: {{ substr($aset->kategori, 0, 15) }}</span>
            <span>TGL: {{ $aset->tanggal_perolehan ? $aset->tanggal_perolehan->format('d/m/Y') : '-' }}</span>
        </div>

    </div>

</body>
</html>