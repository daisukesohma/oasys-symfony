import gql from 'graphql-tag'
import {PROGRAM_INDIVIDUAL_FRAGMENT} from "@/graphql/program/program-individual-fragment";

export const CREATE_PROGRAM_COACHING_INDIVIDUAL = gql`
    mutation createProgramCoachingIndividual (
        $name: String!,
        $description: String!,
        $type: String!,
        $userIds: [String!]!,
        $coachId: String,
        $modelId: String,
        $firstName: String!,
        $lastName: String!,
        $email: String!,
        $phone: String!,
        $period: Int,
        $companyId: String,
    ) {
        createProgramCoachingIndividual (
            name: $name,
            description: $description,
            type: $type,
            userIds: $userIds,
            coachId: $coachId,
            modelId: $modelId,
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            phone: $phone,
            period: $period,
            companyId: $companyId
        ) {
            ...ProgramIndividualFragment
        }
    }
    ${PROGRAM_INDIVIDUAL_FRAGMENT}
`;
