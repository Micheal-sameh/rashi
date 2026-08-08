export default function TablePicker({ tables, selectedTable, onSelect }) {
    return (
        <div>
            <h2 className="text-lg font-semibold text-gray-900 mb-1">Choose a table</h2>
            <p className="text-sm text-gray-500 mb-4">Pick the data source for your report.</p>

            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                {Object.entries(tables).map(([tableName, tableConfig]) => (
                    <button
                        key={tableName}
                        type="button"
                        onClick={() => onSelect(tableName)}
                        className={`text-left rounded-lg border p-4 transition ${
                            selectedTable === tableName
                                ? 'border-blue-600 ring-2 ring-blue-100 bg-blue-50'
                                : 'border-gray-200 hover:border-blue-300'
                        }`}
                    >
                        <div className="font-medium text-gray-900">{tableConfig.label}</div>
                        <div className="text-xs text-gray-500 mt-1">
                            {Object.keys(tableConfig.columns).length} columns
                        </div>
                    </button>
                ))}
            </div>
        </div>
    );
}
