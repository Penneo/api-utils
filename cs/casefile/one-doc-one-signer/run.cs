using System;
using Penneo;
using System.Collections.Generic;
using System.Linq;
using System.Diagnostics;

namespace Penneo
{
    public class CreateSigningRequestLink
    {
        public static void Main(string[] args)
        {
            if (args.Length != 4) {
                Console.WriteLine("Parameters required: endpoint, key, secret, file");
                Environment.Exit(-1);
            }

            string endpoint = args[0];
            string key      = args[1];
            string secret   = args[2];
            string file     = args[3];

            PenneoConnector.Initialize(key, secret, endpoint);
            PenneoConnector.SetLogger(new Logger());
            run(file);
        }

        public static void run(String file)
        {
            // Case file
            //
            var cf = new CaseFile("Sample Case File");
            // cf.ExpireAt  = new DateTime(2020, 1, 1, 1, 1, 1);
            // cf.SensitiveData = false;
            // cf.DisableNotificationsOwner = false;
            // cf.SignOnMeeting = false;
            cf.Persist();

            Console.WriteLine("Case file : " + cf.Id);

            // Add to a folder
            //
            // var folders = Query.FindAll<Folder>();
            // var folder = folders.First();
            // folder.AddCaseFile(cf);

            // Document
            //
            var doc = new Document(cf, "Sample Document", file);
            doc.MakeSignable();
            doc.Persist();

            if (doc.Id == null) {
                Console.WriteLine("Unable to create a document");
                return;
            }

            // Signer
            //
            var signer = new Signer(cf, "John Doe");
            signer.OnBehalfOf = "Acme Corporation";
            // signer.SocialSecurityNumber = "0101501111";
            // signer.VATIdentificationNumber = 12345678;
            signer.Persist();


            // Signature Line
            //
            var sigLine = new SignatureLine(doc, "dummy-signer-role") {
                SignOrder = 0
            };
            sigLine.Persist();

            if (sigLine.Id == null) {
                Console.WriteLine("Unable to create a signature line");
                return;
            }

            // Map the signer to the document using the signature line
            //
            sigLine.SetSigner(signer);

            // Update the signing request
            //
            var signingRequest = signer.GetSigningRequest();
            if (signingRequest.Id == null) {
                Console.WriteLine("Unable to create the signing request");
                return;
            }

            // [Optional] Send emails through Penneo
            //
            // signingRequest.Email = "john@doe.com";
            //
            // signingRequest.EmailSubject = "Contract for signing";
            // signingRequest.EmailText = "Dear john. Please sign the contract.";
            //
            // signingRequest.CompletedEmailSubect = "Completed the case file: {{casefile.title}}";
            // signingRequest.CompletedEmailText = "Dear john. Case file is completed: {{casefile.title}}.";

            // [Optional] Access Control
            // Enable access control if you have specified a Social security
            // number / VAT Identification Number for the Signer
            //
            // signingRequest.AccessControl = true;

            // [Optional] Use touch signatures
            //
            // signingRequest.EnableInsecureSigning = true;

            // [Optional] Redirect after signing
            //
            // signingRequest.SuccessUrl = "https://app.penneo.com/login";
            // signingRequest.FailUrl    = "enter url to go to after failure here";

            signingRequest.Persist();

            // Create the signing request link
            //
            var link = signingRequest.GetLink();

            // Active the Case file (and send signing requests if email details
            // are provided)
            //
            cf.Send();

            // Print the link
            //
            Console.WriteLine(link);
        }
    }

    internal class Logger : ILogger
    {
        public void Log(string message, LogSeverity severity)
        {
            // Console.WriteLine(severity + ": " + message);
        }
    }
}
