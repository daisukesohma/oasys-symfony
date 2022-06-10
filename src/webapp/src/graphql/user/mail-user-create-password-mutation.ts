import gql from 'graphql-tag'

export const MAIL_USER_CREATE_PASSWORD = gql`
    mutation mailUserCreatePassword ($userId: String!) {
        mailUserCreatePassword (userId: $userId) {
            id,
            hasReceivedWelcomeMail,
        }
    }
`;
