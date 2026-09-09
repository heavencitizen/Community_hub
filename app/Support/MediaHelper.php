<?php

namespace App\Support;

class MediaHelper
{
    /**
     * Single Source of Truth untuk Foto Profil Pengguna (User Avatar)
     */
    public static function userAvatar(?int $id, ?string $name = null, ?string $customPath = null): string
    {
        if ($customPath) {
            return asset('storage/'.$customPath);
        }

        $avatars = [
            1 => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&auto=format&fit=crop&q=80', // Super Admin
            2 => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&auto=format&fit=crop&q=80', // Rian Maulana (EO & Captain)
            3 => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=300&auto=format&fit=crop&q=80', // Siti Nurhaliza (Member)
            4 => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=300&auto=format&fit=crop&q=80', // Dimas Pratama
            5 => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=300&auto=format&fit=crop&q=80', // Aisyah Putri
        ];

        return $avatars[$id] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=300&auto=format&fit=crop&q=80';
    }

    /**
     * Single Source of Truth untuk Banner Profil Pengguna (User Banner)
     */
    public static function userBanner(?int $id, ?string $customPath = null): string
    {
        if ($customPath) {
            return asset('storage/'.$customPath);
        }

        $banners = [
            1 => 'https://images.unsplash.com/photo-1707343843437-caacff5cfa74?w=1200&auto=format&fit=crop&q=80',
            2 => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200&auto=format&fit=crop&q=80',
            3 => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?w=1200&auto=format&fit=crop&q=80',
            4 => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&auto=format&fit=crop&q=80',
            5 => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=1200&auto=format&fit=crop&q=80',
        ];

        return $banners[$id] ?? 'https://images.unsplash.com/photo-1707343843437-caacff5cfa74?w=1200&auto=format&fit=crop&q=80';
    }

    /**
     * Single Source of Truth untuk Logo Komunitas
     */
    public static function communityLogo(?int $id, ?string $category = null, ?string $customPath = null): string
    {
        if ($customPath) {
            return asset('storage/'.$customPath);
        }

        $logos = [
            1 => 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=300&auto=format&fit=crop&q=80', // Sumatra Runners
            2 => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?w=300&auto=format&fit=crop&q=80', // Vespa Classic
            3 => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=300&auto=format&fit=crop&q=80', // Scuba Diving
            4 => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=300&auto=format&fit=crop&q=80', // Coffee Enthusiast
            5 => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=300&auto=format&fit=crop&q=80', // Streetball Basket
        ];

        return $logos[$id] ?? 'https://images.unsplash.com/photo-1528605248644-14dd04022da1?w=300&auto=format&fit=crop&q=80';
    }

    /**
     * Single Source of Truth untuk Banner Komunitas
     */
    public static function communityBanner(?int $id, ?string $category = null, ?string $customPath = null): string
    {
        if ($customPath) {
            return asset('storage/'.$customPath);
        }

        $banners = [
            1 => 'https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?w=1200&auto=format&fit=crop&q=80', // Sumatra Runners
            2 => 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?w=1200&auto=format&fit=crop&q=80', // Vespa Classic
            3 => 'https://images.unsplash.com/photo-1682687220063-4742bd7fd538?w=1200&auto=format&fit=crop&q=80', // Scuba Diving
            4 => 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=1200&auto=format&fit=crop&q=80', // Coffee Enthusiast
            5 => 'https://images.unsplash.com/photo-1519766304817-4f37bda74a29?w=1200&auto=format&fit=crop&q=80', // Streetball Basket
        ];

        return $banners[$id] ?? 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?w=1200&auto=format&fit=crop&q=80';
    }

    /**
     * Single Source of Truth untuk Banner Event
     */
    public static function eventBanner(?int $id, ?string $customPath = null): string
    {
        if ($customPath) {
            return asset('storage/'.$customPath);
        }

        $banners = [
            1 => 'https://images.unsplash.com/photo-1452626038306-9aae5e071dd3?w=1200&auto=format&fit=crop&q=80', // Event 1: Sunday Morning Fun Run 5K Pantai Padang
            2 => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=1200&auto=format&fit=crop&q=80', // Event 2: Mini Concert Akustik Galang Dana
            3 => 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=1200&auto=format&fit=crop&q=80', // Event 3: Sunmori Gathering Vespa Sitinjau Lauik
            4 => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=1200&auto=format&fit=crop&q=80', // Event 4: Workshop Manual Brew V60 Solok Radjo
            5 => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=1200&auto=format&fit=crop&q=80', // Event 5: Padang 3x3 Streetball Championship & Dunk Contest
            6 => 'https://images.unsplash.com/photo-1682687220063-4742bd7fd538?w=1200&auto=format&fit=crop&q=80', // Event 6: Underwater Coral Cleanup & Dive Clinic
        ];

        return $banners[$id] ?? 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=1200&auto=format&fit=crop&q=80';
    }

    /**
     * Single Source of Truth untuk Thumbnail Artikel & Berita
     */
    public static function articleThumbnail(?int $id, ?string $category = null, ?string $customPath = null): string
    {
        if ($customPath) {
            return asset('storage/'.$customPath);
        }

        $thumbnails = [
            1 => 'https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?w=1000&auto=format&fit=crop&q=80', // Artikel 1: 5 Rute Lari Pagi
            2 => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1000&auto=format&fit=crop&q=80', // Artikel 2: Konservasi Mandeh
        ];

        return $thumbnails[$id] ?? 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?w=1000&auto=format&fit=crop&q=80';
    }

    /**
     * Single Source of Truth untuk Banner Donasi Amal
     */
    public static function donationBanner(?int $id, ?string $customPath = null): string
    {
        if ($customPath) {
            return asset('storage/'.$customPath);
        }

        $banners = [
            1 => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1000&auto=format&fit=crop&q=80', // Donasi 1: Adopsi Terumbu Karang Mandeh
            2 => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=1000&auto=format&fit=crop&q=80', // Donasi 2: Bantuan Sepatu Anak Nelayan
        ];

        return $banners[$id] ?? 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb9?w=1000&auto=format&fit=crop&q=80';
    }

    /**
     * Single Source of Truth untuk Foto Produk Jual Beli Komunitas
     */
    public static function productImage(?int $id, ?string $customPath = null): string
    {
        if ($customPath) {
            return asset('storage/'.$customPath);
        }

        $images = [
            1 => 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=600&auto=format&fit=crop&q=80', // Jersey Lari Resmi
            2 => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600&auto=format&fit=crop&q=80', // Running Belt
            3 => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?w=600&auto=format&fit=crop&q=80', // Spion Vespa Retro
            4 => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=600&auto=format&fit=crop&q=80', // Specialty Coffee Beans
        ];

        return $images[$id] ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80';
    }

    /**
     * Single Source of Truth untuk Foto Barang Lelang Komunitas
     */
    public static function auctionImage(?int $id, ?string $customPath = null): string
    {
        if ($customPath) {
            return asset('storage/'.$customPath);
        }

        $images = [
            1 => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?w=600&auto=format&fit=crop&q=80', // Helm Bogo Vintage Minang
            2 => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=600&auto=format&fit=crop&q=80', // Diving Mask Cressi F1
        ];

        return $images[$id] ?? 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=600&auto=format&fit=crop&q=80';
    }
}
