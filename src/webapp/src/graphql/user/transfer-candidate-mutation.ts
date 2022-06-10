import gql from 'graphql-tag'

export const TRANSFER_CANDIDATE = gql`
    mutation transferCandidate (
        $userId: String!, 
        $coachSpeciality: String!
    ) {
        transferCandidate (
            userId: $userId,
            coachSpeciality: $coachSpeciality,
        ) {
            id,
            hasBeenTransferred,
            coachSpeciality {
                id
            },
            coach {
                id
            }
        }
    }
`;
