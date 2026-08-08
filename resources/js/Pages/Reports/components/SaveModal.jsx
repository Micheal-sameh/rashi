import { useState } from 'react';

export default function SaveModal({ open, initialName, initialDescription, onClose, onSave }) {
    const [name, setName] = useState(initialName ?? '');
    const [description, setDescription] = useState(initialDescription ?? '');

    if (!open) return null;

    return (
        <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div className="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">Save report</h3>

                <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input
                    type="text"
                    className="w-full rounded-md border-gray-300 mb-4"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    required
                />

                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                    className="w-full rounded-md border-gray-300 mb-4"
                    rows={3}
                    value={description}
                    onChange={(e) => setDescription(e.target.value)}
                />

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
                        disabled={!name.trim()}
                        onClick={() => onSave({ name, description })}
                        className="px-4 py-2 text-sm rounded-md bg-blue-600 text-white disabled:opacity-40"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    );
}
