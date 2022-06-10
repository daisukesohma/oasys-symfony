import gql from 'graphql-tag'

export const USERS_TO_ASSOCIATE_TO_PROGRAM = gql`
    query usersToAssociateToProgram ($search: String, $companyId: String) {
        usersToAssociateToProgram (search: $search, companyId: $companyId) {
            items {
                id,
                firstName,
                lastName,
                nFirstName,
                nLastName,
                nEmail,
                nPhone,
            },
            count,
        }
    }
`;
