import gql from 'graphql-tag'

export const START_SIGN_DOCUMENT = gql`
    mutation startSignDocument (
        $id: String!,
    ) {
        startSignDocument (
            id: $id,
        ) {
            items {
                memberId
                statusSignature
            }
        }
    }
`;
