import gql from 'graphql-tag'
import {PROGRAM_FRAGMENT} from './program-fragment';
import {PROGRAM_INDIVIDUAL_FRAGMENT} from './program-individual-fragment';

export const PROGRAM_BY_ID = gql`
    query programById ($programId: String!) {
        programById (programId: $programId) {
            ...ProgramFragment,
            ...on ProgramCoachingIndividual {
                ...ProgramIndividualFragment
            },
            ...on ProgramPic {
                linkId
                appointmentTimeLimit
                inscriptionText
            }
            users {
                items {
                    id
                    firstName
                    lastName
                }
            },
            documents {
                id,
                name,
            },
        }
    }
    ${PROGRAM_FRAGMENT}
    ${PROGRAM_INDIVIDUAL_FRAGMENT}
`;
