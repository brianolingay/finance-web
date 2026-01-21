import { type SharedData } from '@/types';

export function canManageFinance(abilities?: SharedData['abilities']) {
    return Boolean(abilities?.manageFinance);
}

export function canManageInventory(abilities?: SharedData['abilities']) {
    return Boolean(abilities?.manageInventory);
}

export function canManageCashiers(abilities?: SharedData['abilities']) {
    return Boolean(abilities?.manageCashiers);
}

export function canCreateSale(abilities?: SharedData['abilities']) {
    return Boolean(abilities?.createSale);
}
