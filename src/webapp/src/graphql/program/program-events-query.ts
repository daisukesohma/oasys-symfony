import gql from 'graphql-tag'
import {PROGRAM_FRAGMENT} from './program-fragment';
import {PROGRAM_INDIVIDUAL_FRAGMENT} from './program-individual-fragment';

export const PROGRAM_EVENTS = gql`
    query programEvents ($programId: String!, $limit: Int, $offset: Int) {
        programById (programId: $programId) {
            id,
            eventsOrderedByDate {
                items (limit: $limit, offset: $offset) {
                    id,
                    name,
                    description,
                    status,
                    type,
                    dateEvent,
                    dateEventEnd,
                    updatedAt,
                },
                count,
            },
        }
    }
`;
