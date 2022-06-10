import gql from 'graphql-tag'
import {PROGRAM_FRAGMENT} from './program-fragment';

export const CREATE_PROGRAM_OUTPLACEMENT = gql`
    mutation createProgramOutplacement (
        $name: String!,
        $description: String!,
        $userIds: [String!]!,
        $coachIds: [String!]!,
        $modelId: String,
        $period: Int,
        $companyId: String,
        $endSupportEmail: Boolean,
    ) {
        createProgramOutplacement (
            name: $name,
            description: $description,
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
