import { useState, useEffect } from 'react';

const DAYS_OF_WEEK = [
    { value: 0, label: 'Sunday' },
    { value: 1, label: 'Monday' },
    { value: 2, label: 'Tuesday' },
    { value: 3, label: 'Wednesday' },
    { value: 4, label: 'Thursday' },
    { value: 5, label: 'Friday' },
    { value: 6, label: 'Saturday' },
];

export default function ScheduleModal({ open, initialSchedule, onClose, onSave }) {
    const [frequency, setFrequency] = useState('daily');
    const [time, setTime] = useState('09:00');
    const [dayOfWeek, setDayOfWeek] = useState(1);
    const [dayOfMonth, setDayOfMonth] = useState(1);
    const [recipients, setRecipients] = useState('');
    const [isActive, setIsActive] = useState(true);

    useEffect(() => {
        if (!initialSchedule) return;

        setFrequency(initialSchedule.frequency ?? 'daily');
        setTime(initialSchedule.time ?? '09:00');
        setDayOfWeek(initialSchedule.day_of_week ?? 1);
        setDayOfMonth(initialSchedule.day_of_month ?? 1);
        setRecipients((initialSchedule.recipients ?? []).join(', '));
        setIsActive(initialSchedule.is_active ?? true);
    }, [initialSchedule, open]);

    if (!open) return null;

    const handleSave = () => {
        onSave({
            frequency,
            time,
            day_of_week: frequency === 'weekly' ? dayOfWeek : null,
            day_of_month: frequency === 'monthly' ? dayOfMonth : null,
            recipients: recipients
                .split(',')
                .map((email) => email.trim())
                .filter(Boolean),
            is_active: isActive,
        });
    };

    return (
        <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div className="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">Schedule report</h3>

                <label className="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                <select
                    className="w-full rounded-md border-gray-300 mb-4"
                    value={frequency}
                    onChange={(e) => setFrequency(e.target.value)}
                >
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>

                {frequency === 'weekly' && (
                    <>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Day of week</label>
                        <select
                            className="w-full rounded-md border-gray-300 mb-4"
                            value={dayOfWeek}
                            onChange={(e) => setDayOfWeek(Number(e.target.value))}
                        >
                            {DAYS_OF_WEEK.map((day) => (
                                <option key={day.value} value={day.value}>
                                    {day.label}
                                </option>
                            ))}
                        </select>
                    </>
                )}

                {frequency === 'monthly' && (
                    <>
                        <label className="block text-sm font-medium text-gray-700 mb-1">Day of month</label>
                        <input
                            type="number"
                            min={1}
                            max={31}
                            className="w-full rounded-md border-gray-300 mb-4"
                            value={dayOfMonth}
                            onChange={(e) => setDayOfMonth(Number(e.target.value))}
                        />
                    </>
                )}

                <label className="block text-sm font-medium text-gray-700 mb-1">Time</label>
                <input
                    type="time"
                    className="w-full rounded-md border-gray-300 mb-4"
                    value={time}
                    onChange={(e) => setTime(e.target.value)}
                />

                <label className="block text-sm font-medium text-gray-700 mb-1">
                    Recipients (comma separated emails)
                </label>
                <textarea
                    className="w-full rounded-md border-gray-300 mb-4"
                    rows={2}
                    value={recipients}
                    onChange={(e) => setRecipients(e.target.value)}
                    placeholder="jane@company.com, john@company.com"
                />

                <label className="flex items-center gap-2 mb-4">
                    <input
                        type="checkbox"
                        className="rounded text-blue-600"
                        checked={isActive}
                        onChange={(e) => setIsActive(e.target.checked)}
                    />
                    <span className="text-sm text-gray-700">Active</span>
                </label>

                <div className="flex justify-end gap-2">
                    <button
                        type="button"
                        onClick={onClose}
                        className="px-4 py-2 text-sm rounded-md border border-gray-300"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        onClick={handleSave}
                        className="px-4 py-2 text-sm rounded-md bg-blue-600 text-white"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    );
}
