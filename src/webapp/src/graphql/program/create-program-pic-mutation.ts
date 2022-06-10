import gql from 'graphql-tag'
import {PROGRAM_FRAGMENT} from './program-fragment';

export const CREATE_PROGRAM_PIC = gql`
    mutation createProgramPic (
        $name: String!,
        $description: String!,
        $coachIds: [String!]!,
        $modelId: String,
        $period: Int,
        $companyId: String,
        $endSupportEmail: Boolean
        $appointmentTimeLimit: Int,
        $inscriptionText: String
    ) {
        createProgramPic (
            name: $name,
            description: $description,
            coachIds: $coachIds,
            modelId: $modelId,
            period: $period,
            companyId: $companyId
            endSupportEmail: $endSupportEmail
            appointmentTimeLimit: $appointmentTimeLimit, 
            inscriptionText: $inscriptionText
        ) {
            ...ProgramFragment
            ...on ProgramPic {
                linkId
                appointmentTimeLimit
                inscriptionText
            }
        }
    }
    ${PROGRAM_FRAGMENT}
`;
