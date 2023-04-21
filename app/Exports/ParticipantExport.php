<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ParticipantExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::all();
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->email,
            $user->id,
        ];
    }

    public function headings(): array
    {
        return [
            'email',
            'user_id'
        ];
    }
}
