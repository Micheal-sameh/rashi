import { useEffect, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import axios from 'axios';
import StepIndicator from './components/StepIndicator';
import TablePicker from './components/TablePicker';
import ColumnPicker from './components/ColumnPicker';
import FilterBuilder from './components/FilterBuilder';
import ResultsGrid from './components/ResultsGrid';
import SaveModal from './components/SaveModal';
import ShareModal from './components/ShareModal';
import ScheduleModal from './components/ScheduleModal';

export default function Builder({ report, tables, operators, users }) {
    const { auth, flash } = usePage().props;

    const [step, setStep] = useState(report ? 4 : 1);
    const [tableName, setTableName] = useState(report?.table_name ?? '');
    const [columns, setColumns] = useState(report?.columns ?? []);
    const [filters, setFilters] = useState(report?.filters ?? []);
    const [sortColumn, setSortColumn] = useState(report?.sort_column ?? null);
    const [sortDirection, setSortDirection] = useState(report?.sort_direction ?? 'asc');
    const [page, setPage] = useState(1);
    const [result, setResult] = useState(null);
    const [loading, setLoading] = useState(false);

    const [saveModalOpen, setSaveModalOpen] = useState(false);
    const [shareModalOpen, setShareModalOpen] = useState(false);
    const [scheduleModalOpen, setScheduleModalOpen] = useState(false);

    const tableConfig = tableName ? tables[tableName] : null;
    const isOwner = !report || report.created_by === auth.user?.id;

    const runReport = (targetPage = 1) => {
        if (!tableName || columns.length === 0) return;

        setLoading(true);
        axios
            .post(route('reports.run'), {
                table_name: tableName,
                columns,
                filters,
                sort_column: sortColumn,
                sort_direction: sortDirection,
                page: targetPage,
            })
            .then((response) => {
                setResult(response.data);
                setPage(targetPage);
            })
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        if (report) {
            runReport(1);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const goToResults = () => {
        setStep(4);
        runReport(1);
    };

    const onSortChange = (column) => {
        const nextDirection = sortColumn === column && sortDirection === 'asc' ? 'desc' : 'asc';
        setSortColumn(column);
        setSortDirection(nextDirection);
    };

    useEffect(() => {
        if (step === 4 && result) {
            runReport(1);
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [sortColumn, sortDirection]);

    const payload = () => ({
        table_name: tableName,
        columns,
        filters,
        sort_column: sortColumn,
        sort_direction: sortDirection,
    });

    const handleSave = ({ name, description }) => {
        const data = { name, description, ...payload() };

        if (report) {
            router.put(route('reports.update', report.id), data, {
                onSuccess: () => setSaveModalOpen(false),
            });
        } else {
            router.post(route('reports.store'), data, {
                onSuccess: () => setSaveModalOpen(false),
            });
        }
    };

    const handleShare = (userIds) => {
        router.post(
            route('reports.share.store', report.id),
            { user_ids: userIds },
            { onSuccess: () => setShareModalOpen(false), preserveScroll: true }
        );
    };

    const handleSchedule = (scheduleData) => {
        router.post(route('reports.schedule.store', report.id), scheduleData, {
            onSuccess: () => setScheduleModalOpen(false),
            preserveScroll: true,
        });
    };

    return (
        <div className="max-w-5xl mx-auto py-10 px-4">
            {flash?.success && (
                <div className="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-2 text-sm">
                    {flash.success}
                </div>
            )}
            {flash?.error && (
                <div className="mb-4 rounded-md bg-red-50 border border-red-200 text-red-800 px-4 py-2 text-sm">
                    {flash.error}
                </div>
            )}

            <h1 className="text-2xl font-semibold text-gray-900 mb-6">
                {report ? report.name : 'New Report'}
            </h1>

            <StepIndicator currentStep={step} />

            <div className="bg-white rounded-lg border border-gray-200 p-6">
                {step === 1 && (
                    <TablePicker
                        tables={tables}
                        selectedTable={tableName}
                        onSelect={(name) => {
                            setTableName(name);
                            setColumns([]);
                            setFilters([]);
                            setSortColumn(null);
                        }}
                    />
                )}

                {step === 2 && tableConfig && (
                    <ColumnPicker
                        columns={tableConfig.columns}
                        selectedColumns={columns}
                        onChange={setColumns}
                    />
                )}

                {step === 3 && tableConfig && (
                    <FilterBuilder
                        columns={tableConfig.columns}
                        operators={operators}
                        filters={filters}
                        onChange={setFilters}
                    />
                )}

                {step === 4 && tableConfig && (
                    <>
                        <ResultsGrid
                            columns={columns}
                            columnLabels={Object.fromEntries(
                                Object.entries(tableConfig.columns).map(([key, cfg]) => [key, cfg.label])
                            )}
                            result={result}
                            sortColumn={sortColumn}
                            sortDirection={sortDirection}
                            onSortChange={onSortChange}
                            onPageChange={runReport}
                            loading={loading}
                        />

                        <div className="flex items-center gap-3 mt-6 pt-4 border-t border-gray-100">
                            <button
                                type="button"
                                onClick={() => setSaveModalOpen(true)}
                                className="px-4 py-2 text-sm rounded-md bg-blue-600 text-white font-medium hover:bg-blue-700"
                            >
                                Save
                            </button>

                            {report && isOwner && (
                                <>
                                    <button
                                        type="button"
                                        onClick={() => setShareModalOpen(true)}
                                        className="px-4 py-2 text-sm rounded-md border border-gray-300 font-medium"
                                    >
                                        Share
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setScheduleModalOpen(true)}
                                        className="px-4 py-2 text-sm rounded-md border border-gray-300 font-medium"
                                    >
                                        Schedule
                                    </button>
                                </>
                            )}
                        </div>
                    </>
                )}
            </div>

            <div className="flex items-center justify-between mt-6">
                <button
                    type="button"
                    disabled={step === 1}
                    onClick={() => setStep(step - 1)}
                    className="px-4 py-2 text-sm rounded-md border border-gray-300 disabled:opacity-40"
                >
                    Back
                </button>

                {step < 4 && (
                    <button
                        type="button"
                        disabled={
                            (step === 1 && !tableName) ||
                            (step === 2 && columns.length === 0)
                        }
                        onClick={() => (step === 3 ? goToResults() : setStep(step + 1))}
                        className="px-4 py-2 text-sm rounded-md bg-blue-600 text-white font-medium disabled:opacity-40"
                    >
                        {step === 3 ? 'Run Report' : 'Next'}
                    </button>
                )}
            </div>

            <SaveModal
                open={saveModalOpen}
                initialName={report?.name}
                initialDescription={report?.description}
                onClose={() => setSaveModalOpen(false)}
                onSave={handleSave}
            />

            {report && (
                <ShareModal
                    open={shareModalOpen}
                    users={users.filter((u) => u.id !== report.created_by)}
                    sharedUserIds={(report.shared_with ?? []).map((u) => u.id)}
                    onClose={() => setShareModalOpen(false)}
                    onSave={handleShare}
                />
            )}

            {report && (
                <ScheduleModal
                    open={scheduleModalOpen}
                    initialSchedule={report.schedules?.[0]}
                    onClose={() => setScheduleModalOpen(false)}
                    onSave={handleSchedule}
                />
            )}
        </div>
    );
}
