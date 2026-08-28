<div class="row">
    <div class="col-md-6">
        <div class="table-responsive bg-light p-3 rounded mt-3 border">
            <h6 class="text-center text-primary mb-3">الجدولة السنوية (Yearly Schedules)</h6>
            <table class="table table-bordered table-striped text-center gs-4 gy-2 table-sm">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>السنة</th>
                        <th>قسط الإهلاك</th>
                        <th>مجمع الإهلاك</th>
                        <th>القيمة الدفترية</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($yearly_schedule as $row)
                        <tr>
                            <td>{{ $row['year'] }}</td>
                            <td class="text-danger">{{ number_format($row['expense'], 2) }}</td>
                            <td class="text-gray-800 fw-bold">{{ number_format($row['accumulated'], 2) }}</td>
                            <td class="text-success fw-bold">{{ number_format($row['book_value'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-6">
        <div class="table-responsive bg-light p-3 rounded mt-3 border" style="max-height: 400px; overflow-y: auto;">
            <h6 class="text-center text-primary mb-3">الجدولة الشهرية (Monthly Schedules)</h6>
            <table class="table table-bordered table-striped text-center gs-4 gy-2 table-sm">
                <thead class="bg-primary text-white" style="position: sticky; top: 0; z-index: 1;">
                    <tr>
                        <th>السنة</th>
                        <th>الشهر</th>
                        <th>قسط الإهلاك</th>
                        <th>مجمع الإهلاك</th>
                        <th>القيمة الدفترية</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthly_schedule as $row)
                        <tr>
                            <td>{{ $row['year'] }}</td>
                            <td>{{ str_pad($row['month'], 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="text-danger">{{ number_format($row['expense'], 2) }}</td>
                            <td class="text-gray-800 fw-bold">{{ number_format($row['accumulated'], 2) }}</td>
                            <td class="text-success fw-bold">{{ number_format($row['book_value'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
