export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
<<<<<<< HEAD
        permissions: Record<string, boolean>;
        roles: string[];
        homeUrl: string;
=======
>>>>>>> d5b831a0675ca0cc56a64701e194a719e3f5ebfd
    };
};
