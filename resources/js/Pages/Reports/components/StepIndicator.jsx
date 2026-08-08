const STEPS = ['Table', 'Columns', 'Filters', 'Results'];

export default function StepIndicator({ currentStep }) {
    return (
        <ol className="flex items-center w-full mb-6">
            {STEPS.map((label, index) => {
                const stepNumber = index + 1;
                const isActive = stepNumber === currentStep;
                const isDone = stepNumber < currentStep;

                return (
                    <li key={label} className="flex-1 flex items-center">
                        <div className="flex items-center gap-2">
                            <span
                                className={`flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold ${
                                    isActive
                                        ? 'bg-blue-600 text-white'
                                        : isDone
                                        ? 'bg-blue-100 text-blue-600'
                                        : 'bg-gray-100 text-gray-400'
                                }`}
                            >
                                {stepNumber}
                            </span>
                            <span
                                className={`text-sm font-medium ${
                                    isActive ? 'text-gray-900' : 'text-gray-400'
                                }`}
                            >
                                {label}
                            </span>
                        </div>
                        {stepNumber < STEPS.length && (
                            <div className="flex-1 h-px bg-gray-200 mx-4" />
                        )}
                    </li>
                );
            })}
        </ol>
    );
}
