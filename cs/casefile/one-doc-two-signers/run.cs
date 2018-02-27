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
            cf.Persist();

            Console.WriteLine("Case file : " + cf.Id);

            // Document
            //
            var doc = new Document(cf, "Sample Document", file);
            doc.MakeSignable();
            doc.Persist();

            if (doc.Id == null) {
                Console.WriteLine("Unable to create a document");
                return;
            }

            // Signers
            //
            var signer1 = new Signer(cf, "John Doe");
            signer1.Persist();
            //
            var signer2 = new Signer(cf, "Jane Doe");
            signer2.Persist();

            // Signature Lines
            //
            // We need one signature line per document-signer relationship
            //
            // Signer 1
            var sigLine1 = new SignatureLine(doc, "signer-1") {
                SignOrder = 0
            };
            sigLine1.Persist();
            sigLine1.SetSigner(signer1);
            //
            // Signer 2
            var sigLine2 = new SignatureLine(doc, "signer-2") {
                // If you want to activate all the signing requests the same
                // time (or send emails at the same time), use the same signing
                // order:
                // SignOrder = 0
                //
                SignOrder = 1
            };
            sigLine2.Persist();
            sigLine2.SetSigner(signer2);

            // Extract the links
            //
            var signingRequest1 = signer1.GetSigningRequest();
            var link1 = signingRequest1.GetLink();

            var signingRequest2 = signer2.GetSigningRequest();
            var link2 = signingRequest2.GetLink();

            // Active the Case file (and send signing requests if email details
            // are provided)
            //
            cf.Send();

            // Print the links
            //
            Console.WriteLine(link1);
            Console.WriteLine(link2);
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
