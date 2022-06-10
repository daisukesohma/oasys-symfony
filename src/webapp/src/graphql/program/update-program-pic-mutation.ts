import gql from 'graphql-tag'
import {PROGRAM_FRAGMENT} from './program-fragment';
import {PROGRAM_INDIVIDUAL_FRAGMENT} from './program-individual-fragment';

export const UPDATE_PROGRAM_PIC = gql`
    mutation updateProgramPic (
        $id: String!,
        $name: String!,
        $description: String!,
        $coachIds: [String!]!,
        $userIds: [String!]!,
        $modelId: String,
        $period: Int,
        $file: Upload,
        $companyId: String
        $appointmentTimeLimit: Int
        $inscriptionText: String
    ) {
        updateProgramPic (
            id: $id,
            name: $name,
            description: $description,
            coachIds: $coachIds,
            userIds: $userIds,
            modelId: $modelId,
            period: $period,
            file: $file,
            companyId: $companyId
            appointmentTimeLimit: $appointmentTimeLimit
            inscriptionText: $inscriptionText
        ) {
            ...ProgramFragment,
            ...on ProgramPic {
                linkId
                appointmentTimeLimit
                inscriptionText
            }
        }
    }
    ${PROGRAM_FRAGMENT}
`;
