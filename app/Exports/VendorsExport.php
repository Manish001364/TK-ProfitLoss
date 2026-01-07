<?php

namespace App\Exports;

use App\Models\PnL\PnlVendor;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VendorsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $userId;
    protected $filters;

    public function __construct($userId, array $filters = [])
    {
        $this->userId = $userId;
        $this->filters = $filters;
    }

    public function query()
    {
        $query = PnlVendor::forUser($this->userId);

        if (!empty($this->filters['type'])) {
            $query->ofType($this->filters['type']);
        }

        return $query->orderBy('full_name');
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Business Name',
            'Type',
            'Email',
            'Phone',
            'Alternate Phone',
            'Business Address',
            'Home Address',
            'Emergency Contact Name',
            'Emergency Contact Phone',
            'Emergency Contact Relation',
            'Bank Name',
            'Bank Account Name',
            'Bank Account Number',
            'Bank IFSC',
            'Bank Branch',
            'PAN Number',
            'GST Number',
            'Tax/VAT Reference',
            'Preferred Payment Cycle',
            'Notes',
            'Status',
            'Created At',
        ];
    }

    public function map($vendor): array
    {
        return [
            $vendor->full_name,
            $vendor->business_name,
            ucfirst($vendor->type),
            $vendor->email,
            $vendor->phone,
            $vendor->alternate_phone,
            $vendor->business_address,
            $vendor->home_address,
            $vendor->emergency_contact_name,
            $vendor->emergency_contact_phone,
            $vendor->emergency_contact_relation,
            $vendor->bank_name,
            $vendor->bank_account_name,
            $vendor->bank_account_number,
            $vendor->bank_ifsc_code,
            $vendor->bank_branch,
            $vendor->pan_number,
            $vendor->gst_number,
            $vendor->tax_vat_reference,
            $vendor->preferred_payment_cycle,
            $vendor->notes,
            $vendor->is_active ? 'Active' : 'Inactive',
            $vendor->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }
}
