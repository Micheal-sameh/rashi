export default function FilterBuilder({ columns, operators, filters, onChange }) {
    const columnEntries = Object.entries(columns);

    const addFilter = () => {
        const [firstColumn] = columnEntries[0] || [];
        const type = firstColumn ? columns[firstColumn].type : 'string';
        const [firstOperator] = operators[type] || [];

        onChange([...filters, { column: firstColumn ?? '', operator: firstOperator ?? '=', value: '' }]);
    };

    const updateFilter = (index, changes) => {
        const next = filters.map((filter, i) => (i === index ? { ...filter, ...changes } : filter));
        onChange(next);
    };

    const removeFilter = (index) => {
        onChange(filters.filter((_, i) => i !== index));
    };

    const onColumnChange = (index, columnName) => {
        const type = columns[columnName]?.type ?? 'string';
        const [firstOperator] = operators[type] || [];
        updateFilter(index, { column: columnName, operator: firstOperator ?? '=' });
    };

    return (
        <div>
            <h2 className="text-lg font-semibold text-gray-900 mb-1">Add filters</h2>
            <p className="text-sm text-gray-500 mb-4">
                Optional — narrow down the results. Leave empty to include everything.
            </p>

            <div className="space-y-3">
                {filters.map((filter, index) => {
                    const type = columns[filter.column]?.type ?? 'string';
                    const allowedOperators = operators[type] || [];

                    return (
                        <div key={index} className="flex flex-wrap items-center gap-2">
                            <select
                                className="rounded-md border-gray-300 text-sm"
                                value={filter.column}
                                onChange={(e) => onColumnChange(index, e.target.value)}
                            >
                                {columnEntries.map(([columnName, columnConfig]) => (
                                    <option key={columnName} value={columnName}>
                                        {columnConfig.label}
                                    </option>
                                ))}
                            </select>

                            <select
                                className="rounded-md border-gray-300 text-sm"
                                value={filter.operator}
                                onChange={(e) => updateFilter(index, { operator: e.target.value })}
                            >
                                {allowedOperators.map((operator) => (
                                    <option key={operator} value={operator}>
                                        {operator}
                                    </option>
                                ))}
                            </select>

                            <input
                                type={type === 'date' ? 'date' : type === 'number' ? 'number' : 'text'}
                                className="rounded-md border-gray-300 text-sm flex-1 min-w-[140px]"
                                placeholder="Value"
                                value={filter.value}
                                onChange={(e) => updateFilter(index, { value: e.target.value })}
                            />

                            <button
                                type="button"
                                onClick={() => removeFilter(index)}
                                className="text-sm text-red-600 hover:text-red-800"
                            >
                                Remove
                            </button>
                        </div>
                    );
                })}
            </div>

            <button
                type="button"
                onClick={addFilter}
                disabled={columnEntries.length === 0}
                className="mt-4 text-sm font-medium text-blue-600 hover:text-blue-800"
            >
                + Add Filter
            </button>
        </div>
    );
}
