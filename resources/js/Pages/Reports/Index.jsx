import { Link, usePage } from '@inertiajs/react';

function ReportRow({ report, tables }) {
    const tableLabel = tables[report.table_name]?.label ?? report.table_name;

    return (
        <tr className="border-b border-gray-100">
            <td className="px-4 py-3 font-medium text-gray-900">{report.name}</td>
            <td className="px-4 py-3 text-gray-600">{tableLabel}</td>
            <td className="px-4 py-3 text-gray-600">{(report.columns ?? []).length}</td>
            <td className="px-4 py-3 text-gray-500">
                {report.updated_at ? new Date(report.updated_at).toLocaleString() : '—'}
            </td>
            <td className="px-4 py-3 text-right">
                <Link
                    href={route('reports.show', report.id)}
                    className="text-sm font-medium text-blue-600 hover:text-blue-800"
                >
                    Open
                </Link>
            </td>
        </tr>
    );
}

function ReportTable({ reports, tables, emptyMessage }) {
    if (reports.length === 0) {
        return (
            <div className="rounded-lg border border-dashed border-gray-300 p-8 text-center text-gray-500 text-sm">
                {emptyMessage}
            </div>
        );
    }

    return (
        <div className="overflow-x-auto rounded-lg border border-gray-200">
            <table className="min-w-full divide-y divide-gray-200 text-sm">
                <thead className="bg-gray-50">
                    <tr>
                        <th className="px-4 py-2 text-left font-medium text-gray-600">Name</th>
                        <th className="px-4 py-2 text-left font-medium text-gray-600">Table</th>
                        <th className="px-4 py-2 text-left font-medium text-gray-600">Columns</th>
                        <th className="px-4 py-2 text-left font-medium text-gray-600">Updated</th>
                        <th className="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    {reports.map((report) => (
                        <ReportRow key={report.id} report={report} tables={tables} />
                    ))}
                </tbody>
            </table>
        </div>
    );
}

export default function Index({ reports, sharedReports, tables }) {
    const { flash } = usePage().props;

    return (
        <div className="max-w-5xl mx-auto py-10 px-4">
            {flash?.success && (
                <div className="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-2 text-sm">
                    {flash.success}
                </div>
            )}

            <div className="flex items-center justify-between mb-6">
                <h1 className="text-2xl font-semibold text-gray-900">Reports</h1>
                <Link
                    href={route('reports.create')}
                    className="px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700"
                >
                    + New Report
                </Link>
            </div>

            <section className="mb-10">
                <h2 className="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                    My Reports
                </h2>
                <ReportTable
                    reports={reports}
                    tables={tables}
                    emptyMessage="You haven't created any reports yet."
                />
            </section>

            <section>
                <h2 className="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                    Shared With Me
                </h2>
                <ReportTable
                    reports={sharedReports}
                    tables={tables}
                    emptyMessage="No reports have been shared with you."
                />
            </section>
        </div>
    );
}
