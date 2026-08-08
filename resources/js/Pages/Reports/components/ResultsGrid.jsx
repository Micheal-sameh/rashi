export default function ResultsGrid({
    columns,
    columnLabels,
    result,
    sortColumn,
    sortDirection,
    onSortChange,
    onPageChange,
    loading,
}) {
    const rows = result?.data ?? [];
    const currentPage = result?.current_page ?? 1;
    const lastPage = result?.last_page ?? 1;
    const total = result?.total ?? 0;

    if (!loading && rows.length === 0) {
        return (
            <div className="rounded-lg border border-dashed border-gray-300 p-10 text-center text-gray-500">
                No results found.
            </div>
        );
    }

    return (
        <div>
            <div className="flex items-center justify-between mb-2">
                <span className="text-sm text-gray-500">{total} row(s)</span>
            </div>

            <div className="overflow-x-auto rounded-lg border border-gray-200">
                <table className="min-w-full divide-y divide-gray-200 text-sm">
                    <thead className="sticky top-0 bg-gray-50">
                        <tr>
                            {columns.map((column) => (
                                <th
                                    key={column}
                                    onClick={() => onSortChange(column)}
                                    className="px-4 py-2 text-left font-medium text-gray-600 cursor-pointer select-none whitespace-nowrap"
                                >
                                    {columnLabels[column] ?? column}
                                    {sortColumn === column && (
                                        <span className="ml-1">{sortDirection === 'asc' ? '↑' : '↓'}</span>
                                    )}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100">
                        {rows.map((row, index) => (
                            <tr key={index} className={index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                                {columns.map((column) => (
                                    <td key={column} className="px-4 py-2 whitespace-nowrap text-gray-700">
                                        {String(row[column] ?? '')}
                                    </td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            <div className="flex items-center justify-between mt-3">
                <button
                    type="button"
                    disabled={currentPage <= 1}
                    onClick={() => onPageChange(currentPage - 1)}
                    className="text-sm px-3 py-1 rounded-md border border-gray-300 disabled:opacity-40"
                >
                    Prev
                </button>
                <span className="text-sm text-gray-600">
                    Page {currentPage} of {lastPage}
                </span>
                <button
                    type="button"
                    disabled={currentPage >= lastPage}
                    onClick={() => onPageChange(currentPage + 1)}
                    className="text-sm px-3 py-1 rounded-md border border-gray-300 disabled:opacity-40"
                >
                    Next
                </button>
            </div>
        </div>
    );
}
