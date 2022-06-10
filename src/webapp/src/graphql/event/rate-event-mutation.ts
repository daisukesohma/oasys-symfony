import gql from 'graphql-tag'

export const RATE_EVENT = gql`
    mutation rateEvent ($eventId: String!, $starsNumber: Int!, $comment: String!) {
        rateEvent (eventId: $eventId, starsNumber: $starsNumber, comment: $comment) {
            starsNumber
        }
    }
`;
