// Coach's bank account for package payments, as on UNIBank's "International payments in
// foreign currency" sheet. Packages are priced and paid in euros (EUR).
export const bank = {
    beneficiary: 'Dejan Badjukov',
    address: 'Bul. Marshal Tito 5/4-7, Strumica, North Macedonia',
    iban: 'MK07240117372707105',
    bic: 'UIBMMK22XXX',
    bankName: 'Universal Investment Bank AD Skopje (UNIBANK)',
    bankAddress: 'Maksim Gorki 6, 1000 Skopje, North Macedonia',
    // Intermediary banks for EUR, only needed if the sender's bank asks for one.
    correspondentsEur: [
        { bic: 'RZBAATWWXXX', name: 'Raiffeisen Bank International AG, Austria' },
        { bic: 'UNCRITMMXXX', name: 'UniCredit S.p.A., Italy' },
        { bic: 'FINVBGSFXXX', name: 'First Investment Bank AD, Bulgaria' },
        { bic: 'PBZGHR2XXXX', name: 'Privredna Banka d.d. Zagreb, Croatia' },
        { bic: 'BCITITMMXXX', name: 'Intesa Sanpaolo S.p.A., Milano, Italy' },
        { bic: 'BYLADEMMXXX', name: 'Bayerische Landesbank, Munich, Germany' },
    ],
};

// "MK07240117372707105" -> "MK07 2401 1737 2707 105", the way banks print it.
export const groupIban = iban => iban.replace(/(.{4})(?=.)/g, '$1 ');
