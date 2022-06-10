import gql from 'graphql-tag'

export const MAIL_PROGRAM_USERS_CREATE_PASSWORD = gql`
    mutation mailProgramUsersCreatePassword ($programId: String!) {
        mailProgramUsersCreatePassword (programId: $programId) {
            id,
            usersHaveBeenInvited
        }
    }
`;
