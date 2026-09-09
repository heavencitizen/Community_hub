@props(['name' => null, 'brand' => null, 'class' => 'h-6 w-auto'])

@php
$raw = strtolower(trim($name ?? $brand ?? ''));

// Map bank/payment aliases to possible filename candidates
$aliasMap = [
    'bca' => ['bca.png', 'bca_va.png', 'bank-bca.png', 'bank bca.png'],
    'bca_va' => ['bca.png', 'bca_va.png', 'bank-bca.png'],
    'mandiri' => ['mandiri.png', 'bank-mandiri.png', 'bank_mandiri.png', 'bank mandiri.png'],
    'mandiri_va' => ['mandiri.png', 'bank-mandiri.png', 'bank_mandiri.png', 'bank mandiri.png'],
    'bank mandiri' => ['mandiri.png', 'bank-mandiri.png', 'bank_mandiri.png', 'bank mandiri.png'],
    'bni' => ['bni.png', 'bni_va.png', 'bank-bni.png', 'bank bni.png'],
    'bni_va' => ['bni.png', 'bni_va.png', 'bank-bni.png', 'bank bni.png'],
    'bri' => ['bri.png', 'bri_va.png', 'bank-bri.png', 'bank bri.png'],
    'bri_va' => ['bri.png', 'bri_va.png', 'bank-bri.png', 'bank bri.png'],
    'btn' => ['btn.png', 'bank-btn.png', 'bank_btn.png', 'bank btn.png'],
    'btn_va' => ['btn.png', 'bank-btn.png', 'bank_btn.png', 'bank btn.png'],
    'bank btn' => ['btn.png', 'bank-btn.png', 'bank_btn.png', 'bank btn.png'],
    'cimb' => ['cimb.png', 'cimb-niaga.png', 'cimb_niaga.png', 'cimb niaga.png'],
    'cimb_va' => ['cimb.png', 'cimb-niaga.png', 'cimb_niaga.png', 'cimb niaga.png'],
    'cimb niaga' => ['cimb.png', 'cimb-niaga.png', 'cimb_niaga.png', 'cimb niaga.png'],
    'cimb_niaga' => ['cimb.png', 'cimb-niaga.png', 'cimb_niaga.png', 'cimb niaga.png'],
    'mega' => ['mega.png', 'bank-mega.png', 'bank_mega.png', 'bank mega.png'],
    'mega_va' => ['mega.png', 'bank-mega.png', 'bank_mega.png', 'bank mega.png'],
    'bank mega' => ['mega.png', 'bank-mega.png', 'bank_mega.png', 'bank mega.png'],
    'maybank' => ['maybank.png', 'may-bank.png', 'may_bank.png', 'may bank.png'],
    'maybank_va' => ['maybank.png', 'may-bank.png', 'may_bank.png', 'may bank.png'],
    'seabank' => ['seabank.png', 'sea-bank.png', 'sea_bank.png', 'sea bank.png'],
    'seabank_va' => ['seabank.png', 'sea-bank.png', 'sea_bank.png', 'sea bank.png'],
    'allobank' => ['allobank.png', 'allo-bank.png', 'allo_bank.png', 'allo bank.png'],
    'allobank_va' => ['allobank.png', 'allo-bank.png', 'allo_bank.png', 'allo bank.png'],
    'allo bank' => ['allobank.png', 'allo-bank.png', 'allo_bank.png', 'allo bank.png'],
    'qris' => ['qris.png', 'qris-nasional.png'],
    'gopay' => ['gopay.png', 'go-pay.png', 'go pay.png'],
    'ovo' => ['ovo.png', 'ovo-payment.png'],
    'dana' => ['dana.png', 'dana-wallet.png'],
    'shopeepay' => ['shopeepay.png', 'shopee-pay.png', 'shopee pay.png'],
];

$candidates = $aliasMap[$raw] ?? [
    "{$raw}.png",
    str_replace([' ', '_'], '-', $raw) . '.png',
    str_replace(['-', '_'], ' ', $raw) . '.png'
];

$matchedFile = null;
foreach ($candidates as $cand) {
    if (file_exists(public_path('images/logos/' . $cand))) {
        $matchedFile = $cand;
        break;
    }
}
@endphp

@if($matchedFile)
    <!-- Automatic User PNG Logo -->
    <img src="{{ asset('images/logos/' . $matchedFile) }}" alt="{{ $raw }}" class="{{ $class }} object-contain block mx-auto select-none" loading="lazy">

@elseif($raw === 'bca' || $raw === 'bca_va')
    <!-- BCA Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 120 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="120" height="40" rx="8" fill="#003D79"/>
        <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="900" font-size="20" letter-spacing="1.5">BCA</text>
    </svg>

@elseif($raw === 'mandiri' || $raw === 'mandiri_va' || $raw === 'bank mandiri')
    <!-- Bank Mandiri Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 120 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="120" height="40" rx="8" fill="#002D62"/>
        <path d="M78 8C78 8 84 14 96 14C108 14 114 8 114 8C114 8 108 20 96 20C84 20 78 8 78 8Z" fill="#F8A100"/>
        <text x="44%" y="56%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="Arial, sans-serif" font-weight="900" font-size="14" letter-spacing="0.5">mandırı</text>
    </svg>

@elseif($raw === 'bni' || $raw === 'bni_va')
    <!-- BNI Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 120 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="120" height="40" rx="8" fill="#F4F6F9" stroke="#E2E8F0"/>
        <text x="36%" y="56%" dominant-baseline="middle" text-anchor="middle" fill="#005E6A" font-family="Arial, sans-serif" font-weight="900" font-size="18" letter-spacing="1">BNI</text>
        <path d="M76 10L94 28H82L70 16L76 10Z" fill="#F15A24"/>
        <path d="M84 10L96 22L92 26L80 14L84 10Z" fill="#005E6A"/>
    </svg>

@elseif($raw === 'bri' || $raw === 'bri_va')
    <!-- BRI Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 120 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="120" height="40" rx="8" fill="#00529C"/>
        <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-weight="900" font-size="18" letter-spacing="1">BRI</text>
    </svg>

@elseif($raw === 'btn' || $raw === 'btn_va' || $raw === 'bank btn')
    <!-- Bank BTN Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 120 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="120" height="40" rx="8" fill="#002D62"/>
        <circle cx="25" cy="20" r="10" fill="#E6A117"/>
        <text x="64%" y="56%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="Arial, sans-serif" font-weight="900" font-size="17" letter-spacing="1.5">BTN</text>
    </svg>

@elseif($raw === 'cimb' || $raw === 'cimb_va' || $raw === 'cimb niaga' || $raw === 'cimb_niaga')
    <!-- CIMB Niaga Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 130 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="130" height="40" rx="8" fill="#780816"/>
        <text x="50%" y="56%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="Arial, sans-serif" font-weight="900" font-size="13" letter-spacing="0.5">CIMB NIAGA</text>
    </svg>

@elseif($raw === 'mega' || $raw === 'mega_va' || $raw === 'bank mega')
    <!-- Bank Mega Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 120 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="120" height="40" rx="8" fill="#F37023"/>
        <text x="50%" y="56%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="Arial, sans-serif" font-weight="900" font-size="15" letter-spacing="1">MEGA</text>
    </svg>

@elseif($raw === 'maybank' || $raw === 'maybank_va')
    <!-- Maybank Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 120 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="120" height="40" rx="8" fill="#FFC80B"/>
        <text x="50%" y="56%" dominant-baseline="middle" text-anchor="middle" fill="#000000" font-family="Arial, sans-serif" font-weight="900" font-size="14" letter-spacing="0.5">Maybank</text>
    </svg>

@elseif($raw === 'seabank' || $raw === 'seabank_va')
    <!-- SeaBank Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 120 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="120" height="40" rx="8" fill="#0057E7"/>
        <text x="50%" y="56%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="Arial, sans-serif" font-weight="900" font-size="14" letter-spacing="0.5">SeaBank</text>
    </svg>

@elseif($raw === 'allobank' || $raw === 'allobank_va' || $raw === 'allo bank')
    <!-- Allo Bank Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 120 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="120" height="40" rx="8" fill="#1C1848"/>
        <text x="50%" y="56%" dominant-baseline="middle" text-anchor="middle" fill="#7551FF" font-family="Arial, sans-serif" font-weight="900" font-size="14" letter-spacing="0.5">allo bank</text>
    </svg>

@elseif($raw === 'qris')
    <!-- QRIS Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 100 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="100" height="40" rx="8" fill="#ED1C24"/>
        <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="Arial, sans-serif" font-weight="900" font-size="18" letter-spacing="1">QRIS</text>
        <rect x="6" y="32" width="88" height="3" rx="1.5" fill="#FFFFFF"/>
    </svg>

@elseif($raw === 'gopay')
    <!-- GoPay Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 110 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="110" height="40" rx="8" fill="#00AED6"/>
        <circle cx="24" cy="20" r="9" fill="white"/>
        <circle cx="24" cy="20" r="4.5" fill="#00AED6"/>
        <text x="64%" y="55%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="Arial, sans-serif" font-weight="900" font-size="15" letter-spacing="0.5">gopay</text>
    </svg>

@elseif($raw === 'ovo')
    <!-- OVO Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 100 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="100" height="40" rx="8" fill="#4C2A86"/>
        <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="Arial, sans-serif" font-weight="900" font-size="19" letter-spacing="3">OVO</text>
    </svg>

@elseif($raw === 'dana')
    <!-- DANA Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 100 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="100" height="40" rx="8" fill="#118EEA"/>
        <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="Arial, sans-serif" font-weight="900" font-size="17" letter-spacing="1.5">DANA</text>
    </svg>

@elseif($raw === 'shopeepay')
    <!-- ShopeePay Logo Fallback SVG -->
    <svg class="{{ $class }}" viewBox="0 0 120 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="120" height="40" rx="8" fill="#EE4D2D"/>
        <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-family="Arial, sans-serif" font-weight="900" font-size="13" letter-spacing="0.5">ShopeePay</text>
    </svg>

@elseif($raw === 'instagram')
    <svg class="{{ $class }}" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <radialGradient id="ig-grad" cx="20%" cy="110%" r="130%">
                <stop offset="0%" stop-color="#FFD600"/>
                <stop offset="20%" stop-color="#FF7A00"/>
                <stop offset="50%" stop-color="#FF0069"/>
                <stop offset="70%" stop-color="#D300C5"/>
                <stop offset="100%" stop-color="#7638FA"/>
            </radialGradient>
        </defs>
        <rect width="40" height="40" rx="10" fill="url(#ig-grad)"/>
        <rect x="9" y="9" width="22" height="22" rx="6" stroke="white" stroke-width="2.5" fill="none"/>
        <circle cx="20" cy="20" r="5" stroke="white" stroke-width="2.5" fill="none"/>
        <circle cx="26" cy="14" r="1.5" fill="white"/>
    </svg>

@elseif($raw === 'whatsapp')
    <svg class="{{ $class }}" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="40" height="40" rx="10" fill="#25D366"/>
        <path d="M20 9C13.9 9 9 13.9 9 20C9 22.1 9.6 24 10.6 25.7L9.4 30.6L14.5 29.4C16.1 30.4 18 31 20 31C26.1 31 31 26.1 31 20C31 13.9 26.1 9 20 9ZM25.2 24.3C24.9 25 23.8 25.6 23.2 25.7C22.7 25.8 22 25.8 19.3 24.7C15.8 23.3 13.6 19.8 13.4 19.6C13.3 19.4 12 17.7 12 15.9C12 14.1 12.9 13.2 13.2 12.8C13.5 12.4 13.9 12.3 14.3 12.3C14.4 12.3 14.6 12.3 14.7 12.3C15.1 12.3 15.3 12.4 15.5 12.8C15.8 13.4 16.3 14.7 16.4 14.8C16.5 15 16.5 15.2 16.4 15.4C16.3 15.6 16.2 15.8 16 16C15.8 16.2 15.7 16.3 15.5 16.5C15.3 16.7 15.1 16.9 15.4 17.3C15.6 17.7 16.4 19 17.6 20.1C19.1 21.4 20.4 21.8 20.8 22C21.1 22.1 21.4 22.1 21.6 21.9C21.8 21.7 22.3 21.1 22.6 20.7C22.8 20.4 23.1 20.4 23.4 20.5C23.7 20.6 25.1 21.3 25.4 21.5C25.7 21.6 25.9 21.7 26 21.8C26 22.1 25.6 23.5 25.2 24.3Z" fill="white"/>
    </svg>

@elseif($raw === 'tiktok')
    <svg class="{{ $class }}" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="40" height="40" rx="10" fill="#000000"/>
        <path d="M26.8 15.2C25.4 15.1 24.2 14.3 23.6 13.1V22.4C23.6 25.5 21.1 28 18 28C14.9 28 12.4 25.5 12.4 22.4C12.4 19.3 14.9 16.8 18 16.8C18.4 16.8 18.8 16.8 19.2 17V19.7C18.8 19.5 18.4 19.4 18 19.4C16.3 19.4 15 20.7 15 22.4C15 24.1 16.3 25.4 18 25.4C19.7 25.4 21 24.1 21 22.4V10H23.6C23.6 11.8 25 13.2 26.8 13.3V15.2Z" fill="#25F4EE"/>
        <path d="M27.2 15.6C25.8 15.5 24.6 14.7 24 13.5V22.8C24 25.9 21.5 28.4 18.4 28.4C15.3 28.4 12.8 25.9 12.8 22.8C12.8 19.7 15.3 17.2 18.4 17.2C18.8 17.2 19.2 17.2 19.6 17.4V20.1C19.2 19.9 18.8 19.8 18.4 19.8C16.7 19.8 15.4 21.1 15.4 22.8C15.4 24.5 16.7 25.8 18.4 25.8C20.1 25.8 21.4 24.5 21.4 22.8V10.4H24C24 12.2 25.4 13.6 27.2 13.7V15.6Z" fill="#FE2C55" style="mix-blend-mode: screen;"/>
    </svg>

@else
    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded bg-slate-100 font-bold text-[10px] text-slate-700 uppercase tracking-wider">{{ $raw }}</span>
@endif
