import gql from 'graphql-tag'

export const CONTACT_US = gql`
    mutation contactUs ($comment: String!) {
        contactUs (comment: $comment) {
            id
        }
    }
`;
