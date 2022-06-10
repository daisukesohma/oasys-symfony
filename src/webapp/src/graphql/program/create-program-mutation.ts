import gql from 'graphql-tag'
import {PROGRAM_FRAGMENT} from './program-fragment';

export const CREATE_PROGRAM = gql`
    mutation createProgram (
        $name: String!,
        $description: String!,
        $type: String!,
        $userIds: [String!]!,
        $coachIds: [String!]!,
        $modelId: String,
        $period: Int,
        $companyId: String,
        $endSupportEmail: Boolean
    ) {
        createProgram (
            name: $name,
            description: $description,
            type: $type,
            userIds: $userIds,
            coachIds: $coachIds,
            modelId: $modelId,
            period: $period,
            companyId: $companyId,
            endSupportEmail: $endSupportEmail
        ) {
            ...ProgramFragment
        }
    }
    ${PROGRAM_FRAGMENT}
`;
