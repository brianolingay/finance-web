import { type ReactNode, useId } from 'react';

import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { cn } from '@/lib/utils';

interface SelectOption {
    value: string;
    label: string;
    description?: string;
}

interface SelectFieldProps {
    name: string;
    label: string;
    value: string;
    onValueChange: (value: string) => void;
    options: SelectOption[];
    placeholder?: string;
    error?: string;
    className?: string;
    trailing?: ReactNode;
    id?: string;
}

export function SelectField({
    name,
    label,
    value,
    onValueChange,
    options,
    placeholder,
    error,
    className,
    trailing,
    id,
}: SelectFieldProps) {
    const generatedId = useId();
    const selectId = id ?? generatedId;

    return (
        <div className={cn('flex flex-col gap-2', className)}>
            <div className="flex items-center justify-between gap-2">
                <Label htmlFor={selectId}>{label}</Label>
                {trailing}
            </div>
            <Select value={value} onValueChange={onValueChange}>
                <SelectTrigger id={selectId}>
                    <SelectValue placeholder={placeholder} />
                </SelectTrigger>
                <SelectContent>
                    {options.map((option) => (
                        <SelectItem key={option.value} value={option.value}>
                            <span className="flex flex-col">
                                <span>{option.label}</span>
                                {option.description ? (
                                    <span className="text-xs text-muted-foreground">
                                        {option.description}
                                    </span>
                                ) : null}
                            </span>
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
            <input type="hidden" name={name} value={value} />
            <InputError message={error} />
        </div>
    );
}
