import gql from 'graphql-tag'

export const PROGRAM_PIC_BY_LINK = gql`
    query programPicOfflineTextFromLinkId ($id: String!) {
        programPicOfflineTextFromLinkId (id: $id) {
            inscriptionText
        }
    }
`;
