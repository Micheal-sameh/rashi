import { useState, useEffect } from 'react';

export default function ShareModal({ open, users, sharedUserIds, onClose, onSave }) {
    const [selected, setSelected] = useState(sharedUserIds ?? []);

    useEffect(() => {
        setSelected(sharedUserIds ?? []);
    }, [sharedUserIds, open]);

    if (!open) return null;

    const toggle = (userId) => {
        setSelected((prev) =>
            prev.includes(userId) ? prev.filter((id) => id !== userId) : [...prev, userId]
        );
    };

    return (
        <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div className="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">Share report</h3>

                <div className="max-h-64 overflow-y-auto border border-gray-200 rounded-md divide-y">
                    {users.map((user) => (
                        <label key={user.id} className="flex items-center gap-2 px-3 py-2 cursor-pointer">
                            <input
                                type="checkbox"
                                className="rounded text-blue-600"
                                checked={selected.includes(user.id)}
                                onChange={() => toggle(user.id)}
                            />
                            <span className="text-sm text-gray-900">{user.name}</span>
                            <span className="text-xs text-gray-400 ml-auto">{user.email}</span>
                        </label>
                    ))}
                </div>

                <div className="flex justify-end gap-2 mt-4">
                    <button
                        type="button"
                        onClick={onClose}
                        className="px-4 py-2 text-sm rounded-md border border-gray-300"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        onClick={() => onSave(selected)}
                        className="px-4 py-2 text-sm rounded-md bg-blue-600 text-white"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    );
}
