export default function ColumnPicker({ columns, selectedColumns, onChange }) {
    const toggle = (columnName) => {
        if (selectedColumns.includes(columnName)) {
            onChange(selectedColumns.filter((c) => c !== columnName));
        } else {
            onChange([...selectedColumns, columnName]);
        }
    };

    return (
        <div>
            <h2 className="text-lg font-semibold text-gray-900 mb-1">Choose columns</h2>
            <p className="text-sm text-gray-500 mb-4">Select the fields to show in your report.</p>

            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                {Object.entries(columns).map(([columnName, columnConfig]) => (
                    <label
                        key={columnName}
                        className={`flex items-center gap-2 rounded-lg border p-3 cursor-pointer transition ${
                            selectedColumns.includes(columnName)
                                ? 'border-blue-600 bg-blue-50'
                                : 'border-gray-200 hover:border-blue-300'
                        }`}
                    >
                        <input
                            type="checkbox"
                            className="rounded text-blue-600 focus:ring-blue-500"
                            checked={selectedColumns.includes(columnName)}
                            onChange={() => toggle(columnName)}
                        />
                        <span className="text-sm text-gray-900">{columnConfig.label}</span>
                        <span className="ml-auto text-xs text-gray-400">{columnConfig.type}</span>
                    </label>
                ))}
            </div>
        </div>
    );
}
