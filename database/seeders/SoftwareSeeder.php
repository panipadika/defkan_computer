<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoftwareSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya seed jika tabel software masih kosong (agar tidak menimpa data production)
        if (DB::table('software')->count() > 0) {
            $this->command->warn("⚠️  Tabel software sudah berisi data, skip SoftwareSeeder.");
            return;
        }

        $softwareList = [
            // === GAMING ===
            ['nama' => 'Cyberpunk 2077', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=cyberpunk.net', 'kategori' => 'Gaming', 'ram_min' => 16, 'storage_min' => 70, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'GTA V', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=rockstargames.com', 'kategori' => 'Gaming', 'ram_min' => 8, 'storage_min' => 120, 'cpu_min' => '2', 'vga_min' => '2'],
            ['nama' => 'Valorant', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=playvalorant.com', 'kategori' => 'Gaming', 'ram_min' => 8, 'storage_min' => 40, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Dota 2', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=dota2.com', 'kategori' => 'Gaming', 'ram_min' => 8, 'storage_min' => 60, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Minecraft (Shaders)', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=minecraft.net', 'kategori' => 'Gaming', 'ram_min' => 12, 'storage_min' => 10, 'cpu_min' => '2', 'vga_min' => '2'],
            ['nama' => 'Red Dead Redemption 2', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=rockstargames.com', 'kategori' => 'Gaming', 'ram_min' => 12, 'storage_min' => 150, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'PUBG PC', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=pubg.com', 'kategori' => 'Gaming', 'ram_min' => 16, 'storage_min' => 40, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'EA Sports FC 24', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=ea.com', 'kategori' => 'Gaming', 'ram_min' => 12, 'storage_min' => 100, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'Genshin Impact', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=hoyoverse.com', 'kategori' => 'Gaming', 'ram_min' => 16, 'storage_min' => 150, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'Elden Ring', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=bandainamcoent.com', 'kategori' => 'Gaming', 'ram_min' => 16, 'storage_min' => 60, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'Counter-Strike 2', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=counter-strike.net', 'kategori' => 'Gaming', 'ram_min' => 12, 'storage_min' => 85, 'cpu_min' => '2', 'vga_min' => '2'],
            ['nama' => 'League of Legends', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=leagueoflegends.com', 'kategori' => 'Gaming', 'ram_min' => 8, 'storage_min' => 20, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Apex Legends', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=ea.com', 'kategori' => 'Gaming', 'ram_min' => 12, 'storage_min' => 75, 'cpu_min' => '2', 'vga_min' => '2'],
            ['nama' => 'The Witcher 3', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=thewitcher.com', 'kategori' => 'Gaming', 'ram_min' => 8, 'storage_min' => 50, 'cpu_min' => '2', 'vga_min' => '2'],
            ['nama' => 'Forza Horizon 5', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=xbox.com', 'kategori' => 'Gaming', 'ram_min' => 12, 'storage_min' => 110, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'Fortnite', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=epicgames.com', 'kategori' => 'Gaming', 'ram_min' => 12, 'storage_min' => 30, 'cpu_min' => '2', 'vga_min' => '2'],
            ['nama' => 'Assassin\'s Creed Valhalla', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=ubisoft.com', 'kategori' => 'Gaming', 'ram_min' => 12, 'storage_min' => 50, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'Resident Evil 4 Remake', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=capcom.com', 'kategori' => 'Gaming', 'ram_min' => 16, 'storage_min' => 70, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'Hogwarts Legacy', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=hogwartslegacy.com', 'kategori' => 'Gaming', 'ram_min' => 16, 'storage_min' => 85, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'Call of Duty: Warzone', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=callofduty.com', 'kategori' => 'Gaming', 'ram_min' => 16, 'storage_min' => 125, 'cpu_min' => '3', 'vga_min' => '3'],

            // === EDITING ===
            ['nama' => 'Adobe Premiere Pro', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=adobe.com', 'kategori' => 'Editing', 'ram_min' => 16, 'storage_min' => 8, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'DaVinci Resolve', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=blackmagicdesign.com', 'kategori' => 'Editing', 'ram_min' => 16, 'storage_min' => 5, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'After Effects', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=adobe.com', 'kategori' => 'Editing', 'ram_min' => 16, 'storage_min' => 15, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'CapCut PC Pro', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=capcut.com', 'kategori' => 'Editing', 'ram_min' => 8, 'storage_min' => 10, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'FL Studio', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=image-line.com', 'kategori' => 'Editing', 'ram_min' => 8, 'storage_min' => 4, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Adobe Audition', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=adobe.com', 'kategori' => 'Editing', 'ram_min' => 8, 'storage_min' => 4, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Sony Vegas Pro', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=vegascreativesoftware.com', 'kategori' => 'Editing', 'ram_min' => 16, 'storage_min' => 2, 'cpu_min' => '2', 'vga_min' => '2'],
            ['nama' => 'Filmora 12', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=wondershare.com', 'kategori' => 'Editing', 'ram_min' => 8, 'storage_min' => 10, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Audacity', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=audacityteam.org', 'kategori' => 'Editing', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Ableton Live', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=ableton.com', 'kategori' => 'Editing', 'ram_min' => 8, 'storage_min' => 3, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Logic Pro', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=apple.com', 'kategori' => 'Editing', 'ram_min' => 8, 'storage_min' => 6, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'OBS Studio', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=obsproject.com', 'kategori' => 'Editing', 'ram_min' => 8, 'storage_min' => 2, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Camtasia', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=techsmith.com', 'kategori' => 'Editing', 'ram_min' => 8, 'storage_min' => 4, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'CyberLink PowerDirector', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=cyberlink.com', 'kategori' => 'Editing', 'ram_min' => 8, 'storage_min' => 10, 'cpu_min' => '2', 'vga_min' => '2'],
            ['nama' => 'Reaper', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=reaper.fm', 'kategori' => 'Editing', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],

            // === RENDERING & 3D ===
            ['nama' => 'Blender 3D', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=blender.org', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 5, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'Lumion 12', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=lumion.com', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 40, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'AutoCAD 3D', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=autodesk.com', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 10, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'SketchUp + V-Ray', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=sketchup.com', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 10, 'cpu_min' => '2', 'vga_min' => '2'],
            ['nama' => 'Unreal Engine 5', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=unrealengine.com', 'kategori' => 'Rendering & 3D', 'ram_min' => 32, 'storage_min' => 100, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => '3ds Max', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=autodesk.com', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 9, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'Maya 3D', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=autodesk.com', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 8, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'Autodesk Revit', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=autodesk.com', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 30, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'SolidWorks', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=solidworks.com', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 20, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'Cinema 4D', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=maxon.net', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 10, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'Keyshot', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=keyshot.com', 'kategori' => 'Rendering & 3D', 'ram_min' => 8, 'storage_min' => 5, 'cpu_min' => '2', 'vga_min' => '2'],
            ['nama' => 'ArchiCAD', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=graphisoft.com', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 5, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'Twinmotion', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=twinmotion.com', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 30, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'Houdini', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=sidefx.com', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 5, 'cpu_min' => '3', 'vga_min' => '3'],
            ['nama' => 'ZBrush', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=maxon.net', 'kategori' => 'Rendering & 3D', 'ram_min' => 16, 'storage_min' => 8, 'cpu_min' => '3', 'vga_min' => '2'],

            // === DESAIN ===
            ['nama' => 'Adobe Photoshop', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=adobe.com', 'kategori' => 'Desain', 'ram_min' => 8, 'storage_min' => 4, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Adobe Illustrator', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=adobe.com', 'kategori' => 'Desain', 'ram_min' => 8, 'storage_min' => 4, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'CorelDRAW', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=coreldraw.com', 'kategori' => 'Desain', 'ram_min' => 8, 'storage_min' => 3, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Figma (Desktop)', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=figma.com', 'kategori' => 'Desain', 'ram_min' => 8, 'storage_min' => 2, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Adobe Lightroom', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=adobe.com', 'kategori' => 'Desain', 'ram_min' => 8, 'storage_min' => 4, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Adobe InDesign', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=adobe.com', 'kategori' => 'Desain', 'ram_min' => 8, 'storage_min' => 3, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Canva Desktop', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=canva.com', 'kategori' => 'Desain', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'GIMP', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=gimp.org', 'kategori' => 'Desain', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Sketch (macOS)', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=sketch.com', 'kategori' => 'Desain', 'ram_min' => 8, 'storage_min' => 2, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Inkscape', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=inkscape.org', 'kategori' => 'Desain', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Affinity Designer', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=serif.com', 'kategori' => 'Desain', 'ram_min' => 8, 'storage_min' => 3, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Affinity Photo', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=serif.com', 'kategori' => 'Desain', 'ram_min' => 8, 'storage_min' => 3, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Adobe XD', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=adobe.com', 'kategori' => 'Desain', 'ram_min' => 8, 'storage_min' => 2, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Krita', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=krita.org', 'kategori' => 'Desain', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'PaintTool SAI', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=systemax.jp', 'kategori' => 'Desain', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],

            // === KANTORAN ===
            ['nama' => 'Microsoft Office', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=office.com', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 5, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Zoom Meeting', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=zoom.us', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'WPS Office', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=wps.com', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 2, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Notion Desktop', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=notion.so', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Google Chrome', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=google.com', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Microsoft Teams', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=microsoft.com', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 2, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Slack Desktop', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=slack.com', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Discord', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=discord.com', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Trello Desktop', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=trello.com', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Skype', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=skype.com', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Telegram Desktop', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=telegram.org', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'WhatsApp Desktop', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=whatsapp.com', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Mozilla Firefox', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=mozilla.org', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Microsoft Edge', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=microsoft.com', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'LibreOffice', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=libreoffice.org', 'kategori' => 'Kantoran', 'ram_min' => 4, 'storage_min' => 2, 'cpu_min' => '1', 'vga_min' => '1'],

            // === DEVELOPING ===
            ['nama' => 'VS Code', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=visualstudio.com', 'kategori' => 'Developing', 'ram_min' => 8, 'storage_min' => 2, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Android Studio', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=developer.android.com', 'kategori' => 'Developing', 'ram_min' => 16, 'storage_min' => 15, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'Docker Desktop', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=docker.com', 'kategori' => 'Developing', 'ram_min' => 16, 'storage_min' => 10, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'IntelliJ IDEA', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=jetbrains.com', 'kategori' => 'Developing', 'ram_min' => 12, 'storage_min' => 5, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'PyCharm Professional', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=jetbrains.com', 'kategori' => 'Developing', 'ram_min' => 8, 'storage_min' => 5, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'PhpStorm', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=jetbrains.com', 'kategori' => 'Developing', 'ram_min' => 8, 'storage_min' => 4, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'WebStorm', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=jetbrains.com', 'kategori' => 'Developing', 'ram_min' => 8, 'storage_min' => 3, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'CLion', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=jetbrains.com', 'kategori' => 'Developing', 'ram_min' => 8, 'storage_min' => 4, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Sublime Text', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=sublimetext.com', 'kategori' => 'Developing', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Postman Desktop', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=postman.com', 'kategori' => 'Developing', 'ram_min' => 8, 'storage_min' => 2, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Unity 3D', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=unity.com', 'kategori' => 'Developing', 'ram_min' => 16, 'storage_min' => 25, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'Godot Engine', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=godotengine.org', 'kategori' => 'Developing', 'ram_min' => 8, 'storage_min' => 1, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Eclipse IDE', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=eclipse.org', 'kategori' => 'Developing', 'ram_min' => 8, 'storage_min' => 3, 'cpu_min' => '2', 'vga_min' => '1'],
            ['nama' => 'Visual Studio Ent.', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=visualstudio.com', 'kategori' => 'Developing', 'ram_min' => 16, 'storage_min' => 40, 'cpu_min' => '3', 'vga_min' => '2'],
            ['nama' => 'Xcode (macOS)', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=apple.com', 'kategori' => 'Developing', 'ram_min' => 16, 'storage_min' => 40, 'cpu_min' => '3', 'vga_min' => '1'],
            ['nama' => 'GitKraken', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=gitkraken.com', 'kategori' => 'Developing', 'ram_min' => 8, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'DBeaver', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=dbeaver.io', 'kategori' => 'Developing', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'pgAdmin', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=pgadmin.org', 'kategori' => 'Developing', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Navicat', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=navicat.com', 'kategori' => 'Developing', 'ram_min' => 8, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'GitHub Desktop', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=github.com', 'kategori' => 'Developing', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'XAMPP', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=apachefriends.org', 'kategori' => 'Developing', 'ram_min' => 4, 'storage_min' => 2, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Putty', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=putty.org', 'kategori' => 'Developing', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Notepad++', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=notepad-plus-plus.org', 'kategori' => 'Developing', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Wireshark', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=wireshark.org', 'kategori' => 'Developing', 'ram_min' => 4, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
            ['nama' => 'Insomnia', 'icon' => 'https://www.google.com/s2/favicons?sz=128&domain=insomnia.rest', 'kategori' => 'Developing', 'ram_min' => 8, 'storage_min' => 1, 'cpu_min' => '1', 'vga_min' => '1'],
        ];

        $inserted = 0;
        foreach ($softwareList as $item) {
            $swId = DB::table('software')->insertGetId([
                'nama'     => $item['nama'],
                'icon'     => $item['icon'],
                'kategori' => $item['kategori'],
            ]);
            DB::table('requirement_software')->insert([
                'software_id'  => $swId,
                'ram_min'      => $item['ram_min'],
                'storage_min'  => $item['storage_min'],
                'cpu_min'      => $item['cpu_min'],
                'vga_min'      => $item['vga_min'],
            ]);
            $inserted++;
        }
        $this->command->info("✅ {$inserted} Software berhasil ditambahkan.");
        }
}
