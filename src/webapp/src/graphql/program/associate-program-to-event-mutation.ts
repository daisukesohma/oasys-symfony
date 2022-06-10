import gql from 'graphql-tag'
import {PROGRAM_FRAGMENT} from './program-fragment';

export const ASSOCIATE_PROGRAM_TO_EVENT = gql`
    mutation associateProgramToEvent (
        $programId: String!,
        $eventIds: [String!]!,
    ) {
        associateProgramToEvent (
            programId: $programId, 
            eventIds: $eventIds,
        ) {
            ...ProgramFragment
        }
    }
    ${PROGRAM_FRAGMENT}
`;
