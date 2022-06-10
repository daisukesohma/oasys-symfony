import gql from 'graphql-tag'
import {PROGRAM_FRAGMENT} from './program-fragment';
import {PROGRAM_INDIVIDUAL_FRAGMENT} from './program-individual-fragment';

export const UPDATE_PROGRAM = gql`
    mutation updateProgram (
        $id: String!,
        $name: String!,
        $description: String!,
        $type: String!,
        $userIds: [String!]!,
        $coachIds: [String!]!,
        $modelId: String,
        $firstName: String,
        $lastName: String,
        $email: String,
        $phone: String,
        $period: Int,
        $companyId: String,
    ) {
        updateProgram (
            id: $id,
            name: $name,
            description: $description,
            type: $type,
            userIds: $userIds,
            coachIds: $coachIds,
            modelId: $modelId,
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            phone: $phone,
            period: $period,
            companyId: $companyId
        ) {
            ...ProgramFragment,
            ...on ProgramCoachingIndividual {
                ...ProgramIndividualFragment
            },
        }
    }
    ${PROGRAM_FRAGMENT}
    ${PROGRAM_INDIVIDUAL_FRAGMENT}
`;
