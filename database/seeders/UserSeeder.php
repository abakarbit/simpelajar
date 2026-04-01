<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MataKuliah;
use App\Models\Semester;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $gkmp = User::updateOrCreate(
            ['email' => 'gkmp@simpelajar.id'],
            [
                'name'     => 'Admin GKMP',
                'password' => Hash::make('password'),
                'role'     => 'gkmp',
            ]
        );

        $dosen1 = User::updateOrCreate(
            ['email' => 'budi@simpelajar.id'],
            [
                'name'     => 'Dr. Budi Santoso',
                'password' => Hash::make('password'),
                'role'     => 'dosen',
            ]
        );

        $dosen2 = User::updateOrCreate(
            ['email' => 'siti@simpelajar.id'],
            [
                'name'     => 'Siti Rahma, M.Kom',
                'password' => Hash::make('password'),
                'role'     => 'dosen',
            ]
        );

        // Get active semester
        $activeSemester = Semester::getActive();

        // Mata Kuliah 1: Pemrograman Web - taught by Dr. Budi in Ruang 101
        $mk1 = MataKuliah::updateOrCreate(
            ['kode_mk'  => 'TIF301'],
            ['nama_mk'  => 'Pemrograman Web']
        );
        if (!$dosen1->mataKuliah()->where('mata_kuliah_id', $mk1->id)->exists()) {
            $dosen1->mataKuliah()->attach($mk1->id, [
                'lokasi' => 'Ruang 101',
                'semester_id' => $activeSemester?->id,
            ]);
        }

        // Mata Kuliah 2: Basis Data - taught by Dr. Budi in Ruang 102
        $mk2 = MataKuliah::updateOrCreate(
            ['kode_mk'  => 'TIF201'],
            ['nama_mk'  => 'Basis Data']
        );
        if (!$dosen1->mataKuliah()->where('mata_kuliah_id', $mk2->id)->exists()) {
            $dosen1->mataKuliah()->attach($mk2->id, [
                'lokasi' => 'Ruang 102',
                'semester_id' => $activeSemester?->id,
            ]);
        }

        // Mata Kuliah 3: Kecerdasan Buatan - taught by Siti in Ruang 201
        $mk3 = MataKuliah::updateOrCreate(
            ['kode_mk'  => 'TIF401'],
            ['nama_mk'  => 'Kecerdasan Buatan']
        );
        if (!$dosen2->mataKuliah()->where('mata_kuliah_id', $mk3->id)->exists()) {
            $dosen2->mataKuliah()->attach($mk3->id, [
                'lokasi' => 'Ruang 201',
                'semester_id' => $activeSemester?->id,
            ]);
        }
    }
}

