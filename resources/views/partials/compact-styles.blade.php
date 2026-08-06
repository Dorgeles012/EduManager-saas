<style>
    

    /* ---------- Navbar / Topbar ---------- */
    .navbar-fixed {
        height: 52px !important;
    }
    .main-content-with-fixed-nav {
        margin-top: 52px !important;
    }
.navbar-fixed .h-16 {
        height: 52px !important;
    }

    /* ---------- Tailles de police (réduites) ---------- */
    .text-4xl { font-size: 1.5rem !important; line-height: 2rem !important; }
    .text-3xl { font-size: 1.375rem !important; line-height: 1.75rem !important; }
    .text-2xl { font-size: 1.125rem !important; line-height: 1.5rem !important; }
    .text-xl  { font-size: 1rem !important;    line-height: 1.375rem !important; }
    .text-lg  { font-size: .875rem !important; line-height: 1.25rem !important; }
    .text-base{ font-size: .8125rem !important;line-height: 1.1875rem !important; }
    .text-sm  { font-size: .75rem !important;  line-height: 1.0625rem !important; }
    .text-xs  { font-size: .6875rem !important;line-height: 1rem !important; }
    .text-[11px] { font-size: .625rem !important; }
    .text-[10px] { font-size: .5625rem !important; }
    .text-[12px] { font-size: .6875rem !important; }
    .text-[13px] { font-size: .75rem !important; }
    .text-[14px] { font-size: .8125rem !important; }
    .text-[15px] { font-size: .875rem !important; }
    .text-[16px] { font-size: .875rem !important; }
    .text-[18px] { font-size: .9375rem !important; }
    .text-[20px] { font-size: 1rem !important; }
    .text-[24px] { font-size: 1.125rem !important; }

    /* Tailles personnalisées du thème */
    .text-headline-xl { font-size: 1.625rem !important; line-height: 2rem !important; }
    .text-headline-lg { font-size: 1.25rem !important;  line-height: 1.625rem !important; }
    .text-headline-md { font-size: 1.0625rem !important;line-height: 1.375rem !important; }
    .text-headline-lg-mobile { font-size: 1.125rem !important; line-height: 1.5rem !important; }
    .text-body-lg { font-size: .9375rem !important; line-height: 1.375rem !important; }
    .text-body-md { font-size: .875rem !important;  line-height: 1.25rem !important; }
    .text-body-sm { font-size: .8125rem !important; line-height: 1.125rem !important; }
    .text-label-md{ font-size: .75rem !important;   line-height: .9375rem !important; }
    .text-label-sm{ font-size: .6875rem !important; line-height: .875rem !important; }

/* ---------- Paddings ---------- */
    .p-8 { padding: 1.25rem !important; }
    .p-7 { padding: 1.125rem !important; }
    .p-6 { padding: 1rem !important; }
    .p-5 { padding: .75rem !important; }
    .p-4 { padding: .5rem !important; }
    .p-3 { padding: .5rem !important; }
    .p-2 { padding: .375rem !important; }
    .p-1 { padding: .1875rem !important; }
    .p-1\.5 { padding: .25rem !important; }
    .p-0\.5 { padding: .125rem !important; }

    .px-8 { padding-left: 1.25rem !important; padding-right: 1.25rem !important; }
    .px-7 { padding-left: 1.125rem !important; padding-right: 1.125rem !important; }
    .px-6 { padding-left: 1rem !important; padding-right: 1rem !important; }
    .px-5 { padding-left: .75rem !important; padding-right: .75rem !important; }
    .px-4 { padding-left: .625rem !important; padding-right: .625rem !important; }
    .px-3 { padding-left: .5rem !important; padding-right: .5rem !important; }
    .px-2 { padding-left: .375rem !important; padding-right: .375rem !important; }
    .px-1 { padding-left: .25rem !important; padding-right: .25rem !important; }

    .py-8 { padding-top: 1.25rem !important; padding-bottom: 1.25rem !important; }
    .py-7 { padding-top: 1.125rem !important; padding-bottom: 1.125rem !important; }
    .py-6 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
    .py-5 { padding-top: .75rem !important; padding-bottom: .75rem !important; }
    .py-4 { padding-top: .5rem !important; padding-bottom: .5rem !important; }
    .py-3 { padding-top: .5rem !important; padding-bottom: .5rem !important; }
    .py-2 { padding-top: .375rem !important; padding-bottom: .375rem !important; }
    .py-1 { padding-top: .25rem !important; padding-bottom: .25rem !important; }

    .pt-6 { padding-top: 1rem !important; }
    .pt-5 { padding-top: .75rem !important; }
    .pt-4 { padding-top: .625rem !important; }
    .pt-3 { padding-top: .5rem !important; }
    .pt-2 { padding-top: .375rem !important; }
    .pb-6 { padding-bottom: 1rem !important; }
    .pb-4 { padding-bottom: .625rem !important; }
    .pb-3 { padding-bottom: .5rem !important; }
    .pl-6 { padding-left: 1rem !important; }
    .pl-5 { padding-left: .75rem !important; }
    .pl-4 { padding-left: .625rem !important; }
    .pl-3 { padding-left: .5rem !important; }
    .pr-6 { padding-right: 1rem !important; }
    .pr-4 { padding-right: .625rem !important; }
    .pr-3 { padding-right: .5rem !important; }

    /* ---------- Gaps ---------- */
    .gap-8 { gap: 1.25rem !important; }
    .gap-7 { gap: 1.125rem !important; }
    .gap-6 { gap: 1rem !important; }
    .gap-5 { gap: .75rem !important; }
    .gap-4 { gap: .5rem !important; }
    .gap-3 { gap: .5rem !important; }
    .gap-2 { gap: .375rem !important; }
    .gap-1 { gap: .25rem !important; }
    .gap-x-4 { column-gap: .625rem !important; }
    .gap-x-3 { column-gap: .5rem !important; }
    .gap-x-2 { column-gap: .375rem !important; }
    .gap-y-4 { row-gap: .625rem !important; }
    .gap-y-3 { row-gap: .5rem !important; }
    .gap-y-2 { row-gap: .375rem !important; }

    /* ---------- Marges ---------- */
    .m-6 { margin: 1rem !important; }
    .m-4 { margin: .625rem !important; }
    .m-3 { margin: .5rem !important; }
    .m-2 { margin: .375rem !important; }

    .mt-8 { margin-top: 1.25rem !important; }
    .mt-7 { margin-top: 1.125rem !important; }
    .mt-6 { margin-top: 1rem !important; }
    .mt-5 { margin-top: .75rem !important; }
    .mt-4 { margin-top: .625rem !important; }
    .mt-3 { margin-top: .5rem !important; }
    .mt-2 { margin-top: .375rem !important; }
    .mt-1 { margin-top: .25rem !important; }

    .mb-8 { margin-bottom: 1.25rem !important; }
    .mb-7 { margin-bottom: 1.125rem !important; }
    .mb-6 { margin-bottom: 1rem !important; }
    .mb-5 { margin-bottom: .75rem !important; }
    .mb-4 { margin-bottom: .625rem !important; }
    .mb-3 { margin-bottom: .5rem !important; }
    .mb-2 { margin-bottom: .375rem !important; }
    .mb-1 { margin-bottom: .25rem !important; }

    .ml-6 { margin-left: 1rem !important; }
    .ml-4 { margin-left: .625rem !important; }
    .ml-3 { margin-left: .5rem !important; }
    .ml-2 { margin-left: .375rem !important; }
    .mr-6 { margin-right: 1rem !important; }
    .mr-4 { margin-right: .625rem !important; }
    .mr-3 { margin-right: .5rem !important; }
    .mr-2 { margin-right: .375rem !important; }
    .mx-6 { margin-left: 1rem !important; margin-right: 1rem !important; }
    .mx-4 { margin-left: .625rem !important; margin-right: .625rem !important; }
    .my-6 { margin-top: 1rem !important; margin-bottom: 1rem !important; }
    .my-4 { margin-top: .625rem !important; margin-bottom: .625rem !important; }

    /* ---------- Space-y ---------- */
    .space-y-8 > :not([hidden]) ~ :not([hidden]) { --tw-space-y-reverse: 0; margin-top: calc(1.25rem * calc(1 - var(--tw-space-y-reverse))) !important; }
    .space-y-6 > :not([hidden]) ~ :not([hidden]) { --tw-space-y-reverse: 0; margin-top: calc(1rem * calc(1 - var(--tw-space-y-reverse))) !important; }
    .space-y-5 > :not([hidden]) ~ :not([hidden]) { --tw-space-y-reverse: 0; margin-top: calc(.75rem * calc(1 - var(--tw-space-y-reverse))) !important; }
    .space-y-4 > :not([hidden]) ~ :not([hidden]) { --tw-space-y-reverse: 0; margin-top: calc(.5rem * calc(1 - var(--tw-space-y-reverse))) !important; }
    .space-y-3 > :not([hidden]) ~ :not([hidden]) { --tw-space-y-reverse: 0; margin-top: calc(.5rem * calc(1 - var(--tw-space-y-reverse))) !important; }
    .space-y-2 > :not([hidden]) ~ :not([hidden]) { --tw-space-y-reverse: 0; margin-top: calc(.375rem * calc(1 - var(--tw-space-y-reverse))) !important; }
    .space-x-4 > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 0; margin-right: calc(.625rem * var(--tw-space-x-reverse)) !important; margin-left: calc(.625rem * calc(1 - var(--tw-space-x-reverse))) !important; }
    .space-x-3 > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 0; margin-right: calc(.5rem * var(--tw-space-x-reverse)) !important; margin-left: calc(.5rem * calc(1 - var(--tw-space-x-reverse))) !important; }
    .space-x-2 > :not([hidden]) ~ :not([hidden]) { --tw-space-x-reverse: 0; margin-right: calc(.375rem * var(--tw-space-x-reverse)) !important; margin-left: calc(.375rem * calc(1 - var(--tw-space-x-reverse))) !important; }

    /* ---------- Hauteurs / Largeurs ---------- */
    .h-16 { height: 3.5rem !important; }
    .h-14 { height: 3rem !important; }
    .h-13 { height: 2.75rem !important; }
    .h-12 { height: 2.5rem !important; }
    .h-11 { height: 2.25rem !important; }
    .h-10 { height: 2.25rem !important; }
    .h-9 { height: 2rem !important; }
    .h-8 { height: 1.75rem !important; }
    .h-7 { height: 1.5rem !important; }
    .h-6 { height: 1.25rem !important; }
    .h-5 { height: 1.125rem !important; }
    .h-4 { height: 1rem !important; }
    .min-h-10 { min-height: 2.25rem !important; }
    .min-h-9 { min-height: 2rem !important; }

    .w-16 { width: 3.5rem !important; }
    .w-14 { width: 3rem !important; }
    .w-12 { width: 2.5rem !important; }
    .w-11 { width: 2.25rem !important; }
    .w-10 { width: 2.25rem !important; }
    .w-9 { width: 2rem !important; }
    .w-8 { width: 1.75rem !important; }
    .w-7 { width: 1.5rem !important; }
    .w-6 { width: 1.25rem !important; }
    .w-5 { width: 1.125rem !important; }
    .w-4 { width: 1rem !important; }

    /* ---------- Rayons de bordure ---------- */
    .rounded-3xl { border-radius: 1rem !important; }
    .rounded-2xl { border-radius: .75rem !important; }
    .rounded-xl { border-radius: .5rem !important; }
    .rounded-lg { border-radius: .375rem !important; }
    .rounded-md { border-radius: .25rem !important; }
    .rounded-full { border-radius: 9999px !important; }

/* ---------- Champs de saisie (inputs, selects, textareas) ---------- */
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="number"],
    input[type="tel"],
    input[type="date"],
    input[type="time"],
    input[type="search"],
    input[type="url"],
select,
    textarea {
        font-size: .8125rem !important;
        line-height: 1.125rem !important;
        padding-top: .375rem !important;
        padding-bottom: .375rem !important;
        border-radius: .375rem !important;
    }

    /* ---------- Boutons ---------- */
    button,
    .btn {
        font-size: .75rem !important;
    }

    /* ---------- Tableaux compacts ---------- */
    table {
        font-size: .8125rem !important;
    }
    table th,
    table td {
        padding-top: .375rem !important;
        padding-bottom: .375rem !important;
        line-height: 1.125rem !important;
    }
    table th {
        font-size: .75rem !important;
    }

    /* ---------- Icones Material Symbols (taille normale) ---------- */
    .material-symbols-outlined {
        font-size: 1.5rem !important;
    }
    .material-symbols-outlined.material-symbols-outlined {
        font-size: 1.5rem !important;
    }

/* ---------- Sidebar navigation (tailles normales) ---------- */
    aside nav a {
        padding-top: .625rem !important;
        padding-bottom: .625rem !important;
        padding-left: 1.25rem !important;
        padding-right: 1.25rem !important;
    }
    aside nav a .material-symbols-outlined {
        font-size: 1.5rem !important;
    }
    aside nav a span.font-label-md,
    aside nav a .font-label-md {
        font-size: .875rem !important;
    }

/* ---------- Modales ---------- */
    .swal2-popup {
        font-size: .9375rem !important;
    }
</style>
